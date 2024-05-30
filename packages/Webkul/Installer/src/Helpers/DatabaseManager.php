<?php

namespace Webkul\Installer\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Installer\Database\Seeders\ImportsTableSeeder;
use Webkul\Installer\Database\Seeders\DatabaseSeeder as BagistoDatabaseSeeder;

class DatabaseManager
{
    public function __construct(
        protected ProductRepository $productRepository,
    ) {
    }

    /**
     * Check Database Connection.
     */
    public function isInstalled()
    {
        if (! file_exists(base_path('.env'))) {
            return false;
        }

        try {
            DB::connection()->getPDO();

            $isConnected = (bool) DB::connection()->getDatabaseName();

            if (! $isConnected) {
                return false;
            }

            $hasTable = Schema::hasTable('admins');

            if (! $hasTable) {
                return false;
            }

            $userCount = DB::table('admins')->count();

            if (! $userCount) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Drop all the tables and migrate in the database
     *
     * @return void|string
     */
    public function migration()
    {
        try {
            Artisan::call('migrate:fresh');
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Seed the database.
     *
     * @return void|string
     */
    public function seeder($data)
    {
        try {
            app(BagistoDatabaseSeeder::class)->run($data['parameter']);

            $this->storageLink();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Storage Link.
     */
    private function storageLink()
    {
        Artisan::call('storage:link');
    }

    /**
     * Generate New Application Key
     */
    public function generateKey()
    {
        try {
            Artisan::call('key:generate');
        } catch (Exception $e) {
        }
    }

    /**
     * Generate fake product data.
     *
     * @return void|string
     */
    public function seedSampleProducts()
    {
        try {
            app(ImportsTableSeeder::class)->run();
        } catch (Exception $e) {
            dd($e);
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
