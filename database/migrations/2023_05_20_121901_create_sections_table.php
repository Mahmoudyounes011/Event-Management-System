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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venue_id');
            $table->foreign('venue_id')->references('id')->on('venues')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('name_id');
            $table->foreign('name_id')->references('id')->on('names')->onUpdate('cascade')->onDelete('cascade');
            $table->text('description');
            $table->integer('capacity');
            $table->double('price');
            $table->tinyInteger('available')->default(1);
            $table->timestamps();
            $table->index(['id','available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
