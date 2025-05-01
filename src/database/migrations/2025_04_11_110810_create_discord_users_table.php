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
        Schema::create('discord_users', function (Blueprint $table) {
            $table->id();
            $table->string('snowflake')->unique()->index();
            $table->string('user_name')->nullable()->index();
            $table->string('global_name')->nullable()->index();
            $table->string('avatar')->nullable();
            $table->string('locale')->nullable(); //NOTE: discord may send the users locale
            $table->string('timezone')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discord_users');
    }
};
