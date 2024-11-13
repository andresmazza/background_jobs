<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_jobs', function (Blueprint $table) {
            $table->id();
            $table->integer('pid')->unique();
            $table->integer('priority')->default(0);
            $table->integer('status')->default(3);
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('description');
            $table->timestamps();
            $table->integer('finished_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_jobs');
    }
};
