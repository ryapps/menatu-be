<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('binatus', function (Blueprint $table) {
            $table->id();
            $table->string('map');
            $table->string('nama_laundry');
            $table->decimal('rating');
            $table->integer('review');
            $table->string('alamat');
            $table->enum('status',['tutup','segera buka','segera tutup','buka','buka 24 jam']) -> default('tutup');
            $table->string('jadwal');
            $table->string('no_telepon', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('binatu');
    }
}
