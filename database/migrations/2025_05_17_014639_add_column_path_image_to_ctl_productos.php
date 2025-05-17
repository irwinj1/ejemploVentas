<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ctl_productos', function (Blueprint $table) {
            //
            $table->string('path_image')->after('image')->nullable();
            $table->string('image')->change();
            $table->string('image')->nullable()->change();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ctl_productos', function (Blueprint $table) {
            //
        });
    }
};
