<?php

namespace App\Console\Commands;

use App\Services\PepipostV5Service;
use App\Models\Configuration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestPepipostV5Command extends Command
{
    protected $signature = 'test:pepipost-v5 {email : Email tujuan untuk testing} {--subject=Test Pepipost V5 : Subject email} {--content= : Custom content HTML}';
    protected $description = 'Test Pepipost V5 API dengan mengirim email sederhana';

    public function handle()
    {
        $email = $this->argument('email');
        $subject = $this->option('subject');
        $content = $this->option('content');
        
        $this->info('Testing Pepipost V5 API...');
        $this->info('Target Email: ' . $email);
        $this->info('Subject: ' . $subject);
        
        try {
            $pepipostService = new PepipostV5Service();
            $config = Configuration::first();
            
            $this->info('Sender Email: ' . $config->sender_email);
            $this->info('Sender Name: ' . $config->sender_name);
            
            $this->info('Sending email...');
            
            $response = $pepipostService->sendTestEmail($email, $subject, $content);
            
            $this->info('✅ Email berhasil dikirim!');
            $this->info('HTTP Code: ' . $response['http_code']);
            
            if (isset($response['response']['message'])) {
                $this->info('Response: ' . $response['response']['message']);
            }
            
            if (isset($response['response']['data']['message_id'])) {
                $this->info('Message ID: ' . $response['response']['data']['message_id']);
            }
            
            $this->newLine();
            $this->info('Test Pepipost V5 API berhasil!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->newLine();
            $this->error('Test gagal. Silakan periksa:');
            $this->error('1. API Key di file .env');
            $this->error('2. Konfigurasi sender email');
            $this->error('3. Koneksi internet');
            
            Log::error('Test Pepipost V5 failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}