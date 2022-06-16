<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->date('date');
			$table->string('Ref', 192);
			$table->integer('user_id')->index('income_user_id');
			$table->integer('income_category_id')->index('income_category_id');
			$table->integer('warehouse_id')->index('income_warehouse_id');
			$table->string('details', 192);
			$table->float('amount', 18, 2);
			$table->timestamps(6);
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
