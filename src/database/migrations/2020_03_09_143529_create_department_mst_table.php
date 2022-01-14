<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDepartmentMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('department_mst', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('department_name');
			$table->integer('headquarters_id');
			$table->string('department_list_code');
			$table->boolean('status');
			$table->string('note')->nullable();
			$table->timestamps();
			$table->integer('department_code')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('department_mst');
	}

}
