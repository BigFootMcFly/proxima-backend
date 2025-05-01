<?php

use App\Enums\RemainderStatus;
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
        Schema::create('remainders', function (Blueprint $table) {
            $table->id();

            //NOTE: constrained does not work with softdeletes.
            $table->foreignId('discord_user_id')->constrained();

            $table->string('channel_id')->index()->nullable();
            $table->timestamp('due_at')->index();
            $table->string('message');
            $table->enum('status', RemainderStatus::values())
                ->default(RemainderStatus::NEW->value)
                ->index();

            $table->index('discord_user_id');

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
        Schema::dropIfExists('remainders');
    }
};
