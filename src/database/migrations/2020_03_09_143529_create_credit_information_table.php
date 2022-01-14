<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCreditInformationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('credit_information', function(Blueprint $table)
		{
			$table->integer('client_code');
			$table->integer('client_id');
			$table->integer('group_id');
			$table->string('get_data');
			$table->integer('credit_limit');
			$table->integer('transaction');
			$table->integer('transaction_spot');
			$table->integer('company_id');
			$table->string('headquarter_code');
			$table->string('department_code');
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
		Schema::drop('credit_information');
	}

}
