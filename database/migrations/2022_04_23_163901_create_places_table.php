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
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description', 3000);
            $table->decimal('latitude', 10, 7);  // 10 = nb total chiffres, 7 = nb décimales
            $table->decimal('longitude', 10, 7);  // idem
            $table->float('note', 3, 1);  // 3 = nb total chiffres, 1 = nb décimales
            $table->integer('time'); // temps en heures
            $table->enum('difficulty', ['famille', 'amateur', 'sportif']);
            $table->integer('kilometres')->nullable();
            $table->string('adress', 75);
            $table->char('postcode', 5);
            $table->string('city', 50);
            $table->enum('status', ['validé', 'en attente', 'à modifier', 'refusé']);
            $table->text('comment', 1000)->nullable(); // pour l'administrateur
            $table->timestamps();

            // on delete set null => si suppression compte utilisateur, le lieu reste avec un user_id nul
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // on delete cascade => la suppression d'une catégorie entraîne celle de tous les lieux associés
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            $table->foreignId('department_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lieus');
    }
};
