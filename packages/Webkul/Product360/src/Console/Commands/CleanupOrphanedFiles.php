<?php

namespace Webkul\Product360\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Product360\Services\Product360Service;

class CleanupOrphanedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product360:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup orphaned 360 image files without database records';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Starting cleanup of orphaned 360 image files...');

        try {
            $service = app(Product360Service::class);
            $deletedCount = $service->cleanupOrphanedFiles();

            if ($deletedCount > 0) {
                $this->info("Successfully cleaned up {$deletedCount} orphaned file(s).");
            } else {
                $this->info('No orphaned files found.');
            }
        } catch (\Exception $e) {
            $this->error('Failed to cleanup orphaned files: ' . $e->getMessage());
        }
    }
}
