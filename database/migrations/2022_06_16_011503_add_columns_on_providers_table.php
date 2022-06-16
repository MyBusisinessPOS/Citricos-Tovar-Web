<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsOnProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->string('account_number')->nullable()->after('id');
            $table->string('provider')->nullable()->after('account_number');
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
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn('account_number');
            $table->dropColumn('provider');
            $table->dropColumn('rfc');
            $table->dropColumn('use_cfdi');
        });
    }
}
