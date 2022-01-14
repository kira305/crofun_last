<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_mst', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('own_company');
			$table->string('company_name');
			$table->string('logo');
			$table->string('note')->nullable();
			$table->timestamps();
			$table->string('abbreviate_name')->nullable();
			$table->string('save_ol_name')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_mst');
	}

}
