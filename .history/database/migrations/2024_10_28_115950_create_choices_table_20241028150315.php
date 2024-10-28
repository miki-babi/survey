<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChoicesTable extends Migration
{
    public function up()
    {
        Schema::create('choices', function (Blueprint $table) {
            $table->id();
            $table->string('content');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('next_question_id')->nullable();
            $table->foreign('next_question_id')->references('id')->on('questions')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('choices');
    }
}
