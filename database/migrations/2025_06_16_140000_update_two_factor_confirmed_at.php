<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Since we're moving to a new two factor system, we don't need to update this anymore
        // The migration remains just for historical purposes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this specific update since it's a data-only change
    }
};
