<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePositionMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('position_mst', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('position_name');
			$table->boolean('company_look');
			$table->boolean('headquarter_look');
			$table->boolean('department_look');
			$table->boolean('group_look');
			$table->timestamps();
			$table->integer('company_id');
			$table->boolean('mail_flag')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('position_mst');
	}

}
