<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Response;

class SystemController extends Controller
{
    public function clear()
    {
        try {
            Artisan::call('optimize:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            return response()->json(['message' => 'All caches cleared successfully!', 'type' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function migrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return response()->json(['message' => 'Migration completed successfully!', 'output' => Artisan::output(), 'type' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function migrateFresh()
    {
        try {
            Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
            return response()->json(['message' => 'Fresh migration completed successfully!', 'output' => Artisan::output(), 'type' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
