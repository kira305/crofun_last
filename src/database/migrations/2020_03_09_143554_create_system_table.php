<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSystemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system', function(Blueprint $table)
		{
			$table->integer('f_system_info_key', true);
			$table->string('f_setting_group');
			$table->string('f_setting_name');
			$table->string('f_setting_nm');
			$table->integer('f_details_control');
			$table->string('f_dummy01');
			$table->string('f_setting_data');
			$table->integer('f_insert_id');
			$table->integer('f_update_id');
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
		Schema::drop('system');
	}

}
