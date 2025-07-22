<?php

namespace App\Console\Commands;

use App\Models\Birthday;
use App\Models\Contact;
use App\Models\MissYou;
use Illuminate\Console\Command;
use App\Http\Controllers\PepipostMail;
use App\Models\ExcludedEmail;
use App\Models\MailgunLogs;
use Illuminate\Support\Facades\DB;
use App\Models\Configuration;
use App\Traits\LogsSystemCommand;
use Illuminate\Support\Facades\Log;

class EmailLog extends Command
{
    use LogsSystemCommand;

    protected $signature = 'emaillog
                          {--missyou : Process only miss-you emails}
                          {--birthday : Process only birthday emails}
                          {--poststay : Process only post-stay emails}
                            {--fetch-type= : Specify event types to fetch (processed-delivered, opened, all)}
                          {--batch-size=50 : Number of contacts to process per batch}';

    protected $description = 'Fetch and process email delivery logs from Pepipost for post-stay, miss-you, and birthday emails with batch processing';

    private $delayBetweenEmails = 100000; // 0.1 seconds in microseconds

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->startLogging();
        
        try {
            $this->info('Starting EmailLog command...');
            
            // Fetch contacts using original logic
            $contacts = $this->fetchContacts();
            
            if (empty($contacts)) {
                $this->info('No contacts to process.');
                $this->logCommandEnd('emaillog', 'No contacts found to process');
                return 0;
            }
            
            $this->info("Total contacts found: " . count($contacts));
            
            // Process ALL batches in single run
            $this->processAllBatches($contacts);
            
            $this->logCommandEnd('emaillog', 'Successfully processed all email logs in single run');
            
        } catch (\Exception $e) {
            Log::error('EmailLog command failed: ' . $e->getMessage());
            $this->error('Command failed: ' . $e->getMessage());
            
            $this->markFailed($e->getMessage());
            $this->logCommandEnd('emaillog');
            throw $e;
        }
    }

    private function fetchContacts()
    {
        $config = Configuration::first();
        $missyouconfig = MissYou::first();
        $bdayconfig = Birthday::first();
        $fetchType = $this->option('fetch-type') ?? 'all';

        $this->info("Fetching contacts with recent activities (fetch-type: {$fetchType})...");
        $contacts = [];
        
        // Determine which types to process based on options
        $processTypes = [];
        if ($this->option('missyou')) $processTypes[] = 'missyou';
        if ($this->option('birthday')) $processTypes[] = 'birthday';
        if ($this->option('poststay')) $processTypes[] = 'poststay';
        
        // If no specific type is selected, process all
        if (empty($processTypes)) {
            $processTypes = ['missyou', 'birthday', 'poststay'];
        }

        // Get emails that are unsubscribed (regardless of tags) - SELALU DIKECUALIKAN
        $unsubscribedEmails = MailgunLogs::where('event', 'unsubscribed')
            ->pluck('recipient')
            ->toArray();
        
        $this->info('Found ' . count($unsubscribedEmails) . ' unsubscribed emails to exclude');

        // FILTERING BERDASARKAN FETCH-TYPE
        $additionalExcludedEvents = [];
        
        if ($fetchType === 'processed-delivered') {
            // Kecualikan yang sudah opened untuk semua tags
            $additionalExcludedEvents = ['opened'];
            $this->info('Fetch-type: processed-delivered - excluding opened events');
        } elseif ($fetchType === 'opened') {
            // Kecualikan yang sudah delivered/processed untuk semua tags
            $additionalExcludedEvents = ['delivered'];
            $this->info('Fetch-type: opened - excluding delivered/processed events');
        }

        // Fetch contacts based on selected types dengan FILTERING YANG TEPAT
        if (in_array('poststay', $processTypes)) {
            // Get emails that already have final status for poststay specifically
            $poststayProcessedEmails = MailgunLogs::where('tags', 'poststay')
                ->whereIn('event', array_merge(['failed', 'clicked'], $additionalExcludedEvents))
                ->pluck('recipient')
                ->toArray();
            
            $excludedForPoststay = array_merge($unsubscribedEmails, $poststayProcessedEmails);
            
            $poststay = Contact::has('profilesfolio', '>', 0)
                ->whereHas('profilesfolio', function($q){
                    return $q->whereDate('dateco', '>=', \Carbon\Carbon::now()->subDays(15)->format('Y-m-d'))
                        ->whereDate('dateco', '<=', \Carbon\Carbon::now()->format('Y-m-d'));
                })
                ->whereNotIn('email', $excludedForPoststay)
                ->get();
            $this->info(sprintf('Found %d post-stay contacts (after filtering for fetch-type: %s)', $poststay->count(), $fetchType));
            $contacts = array_merge($contacts, $poststay->all());
        }

        if (in_array('missyou', $processTypes)) {
            // Get emails that already have final status for missyou specifically
            $missyouProcessedEmails = MailgunLogs::where('tags', 'missyou')
                ->whereIn('event', array_merge(['failed', 'clicked'], $additionalExcludedEvents))
                ->pluck('recipient')
                ->toArray();
            
            $excludedForMissyou = array_merge($unsubscribedEmails, $missyouProcessedEmails);
            
            $missyou = Contact::has('profilesfolio', '>', 0)
                ->whereHas('profilesfolio', function($q) use ($missyouconfig){
                    return $q->whereDate('dateco', '>=', \Carbon\Carbon::now()->subMonths($missyouconfig->sendafter)->subDays(15)->format('Y-m-d'))
                        ->whereDate('dateco', '<=', \Carbon\Carbon::now()->subMonths($missyouconfig->sendafter)->format('Y-m-d'));
                })
                ->whereNotIn('email', $excludedForMissyou)
                ->get();
            $this->info(sprintf('Found %d miss-you contacts (after filtering for fetch-type: %s)', $missyou->count(), $fetchType));
            $contacts = array_merge($contacts, $missyou->all());
        }

        if (in_array('birthday', $processTypes)) {
            // Get emails that already have final status for birthday specifically
            $birthdayProcessedEmails = MailgunLogs::where('tags', 'birthday')
                ->whereIn('event', array_merge(['failed', 'clicked'], $additionalExcludedEvents))
                ->pluck('recipient')
                ->toArray();
            
            $excludedForBirthday = array_merge($unsubscribedEmails, $birthdayProcessedEmails);
            
            $bday = Contact::has('transaction', '>', 0)
                ->where(DB::raw('DATE_FORMAT(birthday,\'%m-%d\')'), '>=', \Carbon\Carbon::now()->addDays(abs($bdayconfig->sendafter))->subDays(15)->format('m-d'))
                ->where(DB::raw('DATE_FORMAT(birthday,\'%m-%d\')'), '<=', \Carbon\Carbon::now()->addDays(abs($bdayconfig->sendafter))->format('m-d'))
                ->whereNotIn('email', $excludedForBirthday)
                ->get();
            $this->info(sprintf('Found %d birthday contacts (after filtering for fetch-type: %s)', $bday->count(), $fetchType));
            $contacts = array_merge($contacts, $bday->all());
        }
        
        return $contacts;
    }

    private function processAllBatches($contacts)
    {
        // Implementasi Batching - SEMUA BATCH DIPROSES
        $batchSize = (int) $this->option('batch-size');
        $contactChunks = array_chunk($contacts, $batchSize);
        $totalBatches = count($contactChunks);
        
        $this->info(sprintf('Processing ALL %d batches of %d contacts each in single run', $totalBatches, $batchSize));
        
        $processedCount = 0;
        $excludedCount = 0;
        $globalContactIndex = 0;
        
        // Process ALL batches
        foreach($contactChunks as $batchIndex => $contactBatch) {
            $currentBatch = $batchIndex + 1;
            
            $this->info(sprintf('\n=== Processing batch %d of %d (%d contacts) ===', 
                $currentBatch, $totalBatches, count($contactBatch)));
            
            foreach($contactBatch as $contact){
                $globalContactIndex++;
                $this->info(sprintf('Processing contact %d of %d (%s)', 
                    $globalContactIndex, count($contacts), $contact->email));
                
                // ORIGINAL PEPIPOST PROCESSING LOGIC
                $this->processContactWithPepipost($contact, $processedCount, $excludedCount);
                
                // Rate limiting - delay antar contact
                usleep($this->delayBetweenEmails);
            }
            
            $this->info("Completed batch {$currentBatch}");
            
            // Delay antar batch untuk mengurangi load
            if($currentBatch < $totalBatches) {
                $this->info(sprintf('Batch %d completed. Waiting 10 seconds before next batch...', $currentBatch));
                sleep(10);
            }
        }
        
        // Process mailgun logs once at the end (ORIGINAL LOGIC)
        $this->processMailgunLogs();
        
        $this->info(sprintf('\n=== COMPLETED ==='));
        $this->info(sprintf('Processed %d contacts in %d batches', $processedCount, $totalBatches));
        $this->info(sprintf('Excluded: %d contacts', $excludedCount));
    }

    private function processContactWithPepipost($contact, &$processedCount, &$excludedCount, $fetchType = 'all')
    {
        try {
            $pepi = new PepipostMail();
            $config = Configuration::first();
            
            $logs = $pepi->getContactLog($contact, $config->sender_email);
            if(!is_null($logs) && !empty($logs['data'])){
                $data = $logs['data'];
                if($data[0]['tags'][0] == 'poststay' || $data[0]['tags'][0] == 'birthday' || $data[0]['tags'][0] == 'missyou' || $data[0]['tags'][0] == 'prestay'){
                    $email_id = $data[0]['trid'];
                    $recipient = $data[0]['rcptEmail'];
                    $status = $data[0]['status'];
                    $xapiheader=$data[0]['xapiheader'];
                    $urls=[];
                    $url='';
                    $delivery_status = '';
                    $tag=$data[0]['tags'][0];
                    
                    // ORIGINAL LOGIC - TETAP DIPERTAHANKAN
                    if ($status == 'dropped') {
                        $event = 'failed';
                        $delivery_status = $data[0]['remarks'];
                        ExcludedEmail::updateOrCreate(
                            ['email' => $recipient],
                            ['reason'=>$delivery_status]
                        );
                    }elseif ($status == 'open'){
                        $event = 'opened';
                    }elseif($status == 'click'){
                        foreach ($data[0]['clicks'] as $clicks) {
                            array_push($urls, $clicks['link']);
                        }
                        $url = implode(';',$urls);
                        $event = 'clicked';
                    }elseif ($status == 'hardbounce'  || $status == 'invalid') {
                        $delivery_status = $data[0]['remarks'];
                        $event = 'failed';
                        ExcludedEmail::updateOrCreate(
                            ['email' => $recipient],
                            ['reason'=>$delivery_status]
                        );
                    }elseif ($status=='spam') {
                        $event = 'spam';
                    }elseif ($status=='unsubscribe'){
                        $event='unsubscribed';
                        ExcludedEmail::updateOrCreate(
                            ['email' => $recipient],
                            ['reason'=>'The recipient opted out using unsubscribe link']
                        );
                    }else{
                        $event = 'delivered';
                    }
                    
                    // SIMPAN SEMUA EVENT YANG DITEMUKAN (karena sudah difilter di fetchContacts)
                    $time = \Carbon\Carbon::parse($data[0]['requestedTime'])->format('Y-m-d H:i:s');
                    if($data[0]['tags'][1]==env('UNIT')) {
                        MailgunLogs::updateOrCreate(
                            ['email_id'=> $xapiheader,'recipient' => $recipient],
                            ['message_id' => $email_id, 'event' => $event, 'severity' => NULL, 'url' => $url, 'tags' => $tag, 'recipient' => $recipient, 'timestamp' => $time, 'delivery_status' => $delivery_status]
                        );
                        $this->info("Saved event '{$event}' for {$recipient} (fetch-type: {$fetchType})");
                    }
                }
                $processedCount++;
                if(in_array($status, ['dropped', 'hardbounce', 'invalid', 'unsubscribe'])) {
                    $excludedCount++;
                }
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to process contact {$contact->email}: " . $e->getMessage());
            $this->error("Failed to process contact {$contact->email}: " . $e->getMessage());
        }
    }

    private function processMailgunLogs()
    {
        // ORIGINAL MAILGUN LOGS PROCESSING
        $this->info('Processing mailgun logs for excluded emails...');
        $mailgunlogs = MailgunLogs::whereIn('event',['failed','unsubscribed'])->get();
        foreach ($mailgunlogs as $mailgunlog){
            if($mailgunlog->event=='unsubscribed'){
                ExcludedEmail::updateOrCreate(
                    ['email' => $mailgunlog->recipient],
                    ['reason'=>'The recipient opted out using unsubscribe link']
                );
            }else{
                ExcludedEmail::updateOrCreate(
                    ['email' => $mailgunlog->recipient],
                    ['reason'=>$mailgunlog->delivery_status]
                );
            }
        }
    }
}