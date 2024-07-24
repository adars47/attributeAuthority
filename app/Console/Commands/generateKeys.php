<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class generateKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will generate the keys required for this service to run';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       exec("openssl genrsa -out sharingService.pem 1024");
       exec("openssl rsa -in sharingService.pem -pubout -out publickey.crt");
        $this->info("Successfully generated keys");
    }
}
