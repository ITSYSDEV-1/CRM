<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExternalContact;
use App\Models\ExcludedEmail;
use App\Traits\LogsSystemCommand;
use Carbon\Carbon;

class ValidateEmail extends Command
{
    use LogsSystemCommand;

    protected $signature = 'validateemail {--chunk=100 : Number of emails to process per batch}'
                         . ' {--timeout=30 : Maximum execution time in seconds for each validation}';

    protected $description = 'Validates email addresses of external contacts and marks invalid ones for exclusion. '
                           . 'This command processes unvalidated emails, checks their validity using an external '
                           . 'Python script, and updates their status accordingly.';

    protected $changesCount = 0;

    public function handle()
    {
        $this->startLogging();
        $this->info('Starting email validation process...');

        try {
            ExternalContact::where('validated', '=', 'N')
                ->chunk($this->option('chunk'), function ($contacts) {
                    foreach ($contacts as $contact) {
                        $this->validateSingleEmail($contact);
                    }
                });

            if ($this->changesCount > 0) {
                $this->addLogContext('total_changes', $this->changesCount);
                $this->logCommandEnd('validateemail', sprintf('Validated emails with %d changes', $this->changesCount));
            }
            $this->info(sprintf('Email validation completed. Total changes: %d', $this->changesCount));
        } catch (\Exception $e) {
            $this->markFailed($e->getMessage());
            $this->error('Exception during validation: ' . $e->getMessage());
        }

        return 0;
    }

    protected function validateSingleEmail($contact)
    {
        $res = $this->validate($contact->email);
        $significantChange = false;

        if ($res != 'pass') {
            $excluded = ExcludedEmail::updateOrCreate(
                ['email' => $contact->email],
                ['reason' => $res]
            );
            
            if ($excluded->wasRecentlyCreated || $excluded->wasChanged()) {
                $significantChange = true;
                $this->changesCount++;
                $this->warn(sprintf('Email %s excluded. Reason: %s', $contact->email, $res));
                $this->addLogContext('excluded_email', ['email' => $contact->email, 'reason' => $res]);
            }
        }

        $contact->validated = 'Y';
        if ($contact->isDirty()) {
            $significantChange = true;
            $this->changesCount++;
            $contact->save();
            $this->info(sprintf('Email %s validation status updated', $contact->email));
        }

        if ($significantChange) {
            $this->line(sprintf('Significant change for email: %s', $contact->email));
        }
    }

    protected function validate($email)
    {
        $command = sprintf(
            'python3 /usr/local/bin/pmscrm/validateemailphp.py %s --timeout=%d',
            escapeshellarg($email),
            $this->option('timeout')
        );
        return exec($command);
    }
}
