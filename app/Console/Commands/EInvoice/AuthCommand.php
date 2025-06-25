<?php

namespace App\Console\Commands\EInvoice;

use App\Services\EInvoice\AuthService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'einvoice:auth {--force : Force a new token request, even if one is cached}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Authenticates with LHDN MyInvois API and caches the access token.';

    /**
     * Execute the console command.
     */
    public function handle(AuthService $authService)
    {
        if ($this->option('force')) {
            $this->info('Forcing a new token request...');
            Cache::forget(config('services.lhdn_myinvois.token_cache_key'));
        }

        $token = $authService->refreshAccessToken();

        if (! $token) {
            Log::error('Failed to obtain LHDN MyInvois access token. The refresh method returned null or empty.');
            $this->error('Failed to obtain LHDN MyInvois access token (null/empty response).');

            return Command::FAILURE;
        }
        $this->info('token:'.$token);

        return Command::SUCCESS;
    }
}
