<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->boolean('cover')->default(false); // pour le lieu => couverture / pour le user => image de profil
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('size')->nullable();
            $table->enum('status', ['validée', 'en attente', 'refusée']);

            $table->timestamps();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // pas de lieu_id => image de profil ou image de catégorie
            $table->foreignId('place_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
};
