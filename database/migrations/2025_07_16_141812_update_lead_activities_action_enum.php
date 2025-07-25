<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->enum('action', ['commented', 'status_updated', 'assigned', 'created', 'deleted'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->enum('action', ['commented', 'status_updated', 'assigned'])->change();
        });
    }
};
