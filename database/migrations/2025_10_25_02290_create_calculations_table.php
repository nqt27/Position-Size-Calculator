<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalculationsTable extends Migration
{
    public function up()
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->decimal('balance', 18, 4);
            $table->decimal('risk_percent', 5, 2); 
            $table->decimal('stop_loss', 10, 4); 
            $table->string('pair', 20);
            $table->decimal('risk_amount_usd', 18, 4); 
            $table->decimal('pip_value', 10, 4); 
            $table->decimal('position_size', 18, 8); 
            $table->string('account_currency', 3)->default('USD'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calculations');
    }
}
