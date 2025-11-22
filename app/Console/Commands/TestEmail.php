<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Testing email configuration...');
            
            Mail::raw('This is a test email from Laravel application.', function ($message) {
                $message->to('test@example.com')
                        ->subject('Laravel Email Test');
            });
            
            $this->info('✅ Email sent successfully!');
            $this->info('Check your Mailtrap inbox to see the email.');
            
        } catch (\Exception $e) {
            $this->error('❌ Email failed to send: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
/*
latest commit
    */
