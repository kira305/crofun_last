<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contract', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('contract_file');
			$table->integer('company_id');
			$table->timestamps();
			$table->integer('client_id')->nullable();
			$table->integer('group_id')->nullable();
			$table->integer('project_id')->nullable();
			$table->integer('headquarter_id')->nullable();
			$table->integer('department_id')->nullable();
			$table->string('save_sv_name')->nullable();
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
		Schema::drop('contract');
	}

}
