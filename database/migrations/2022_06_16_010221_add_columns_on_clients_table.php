<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsOnClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('account_number')->nullable()->after('id');
            $table->string('client')->nullable()->after('account_number');
            $table->string('rfc')->nullable()->after('email');
            $table->string('use_cfdi')->nullable()->after('rfc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('account_number');
            $table->dropColumn('client');
            $table->dropColumn('rfc');
            $table->dropColumn('use_cfdi');
        });
    }
}
