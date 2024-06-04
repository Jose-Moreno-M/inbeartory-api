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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->integer('inventory_number')->unique();
            $table->string('description');
            $table->string('image');
            $table->string('brand');
            $table->string('model');
            $table->string('serie');
            $table->string('condition');
            $table->string('comments')->nullable();
            $table->boolean('state');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('area_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
