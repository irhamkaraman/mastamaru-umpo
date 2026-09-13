<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateCliToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:cli-token {name=CLI_ADMIN : The name of the token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Sanctum Personal Access Token for CLI tools';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::first();
        if (!$user) {
            $this->error('No users found in the database. Please create an admin user first.');
            return 1;
        }

        $tokenName = $this->argument('name');
        $token = $user->createToken($tokenName);

        $this->info("Token created successfully for user: {$user->email}");
        $this->line("Token Name: {$tokenName}");
        $this->line("Token Value (COPY THIS):");
        $this->info($token->plainTextToken);
        
        return 0;
    }
}
