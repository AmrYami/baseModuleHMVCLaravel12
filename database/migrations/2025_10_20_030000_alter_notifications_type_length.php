<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            try {
                DB::statement('ALTER TABLE notifications MODIFY COLUMN type VARCHAR(64) NULL');
            } catch (\Throwable $e) {
                // ignore if DBMS does not support; tests will reveal if further action needed
            }
        }
    }

    public function down(): void
    {
        // no-op (do not shrink column back)
    }
};

