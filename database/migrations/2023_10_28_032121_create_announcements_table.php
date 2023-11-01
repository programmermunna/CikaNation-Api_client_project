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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->string('message')->nullable();
            $table->boolean('status')->default(1)->comment('0= inactive, 1=active');
            $table->foreignId('created_by')->nullable()->constrained('users','id');
            $table->index('status','announcement_status');
            $table->index('created_at','announcement_created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
