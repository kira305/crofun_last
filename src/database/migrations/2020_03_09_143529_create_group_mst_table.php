<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_mst', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('group_name');
			$table->integer('department_id');
			$table->string('group_list_code');
			$table->boolean('status');
			$table->string('note')->nullable();
			$table->timestamps();
			$table->integer('group_code')->nullable();
			$table->string('cost_code')->nullable();
			$table->string('cost_name')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_mst');
	}

}
