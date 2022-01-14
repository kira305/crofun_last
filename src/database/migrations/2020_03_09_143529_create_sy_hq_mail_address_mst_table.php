<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSyHqMailAddressMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sy_hq_mail_address_mst', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('headquarters_code');
			$table->string('department_code');
			$table->string('group_code');
			$table->string('sales_management_code');
			$table->string('sales_management');
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
		Schema::drop('sy_hq_mail_address_mst');
	}

}
