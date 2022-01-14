<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_mst', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('usr_code');
			$table->string('usr_name');
			$table->integer('rule');
			$table->string('pw');
			$table->string('email_address');
			$table->integer('company_id');
			$table->integer('headquarter_id');
			$table->integer('department_id');
			$table->integer('group_id');
			$table->boolean('retire');
			$table->timestamps();
			$table->integer('position_id')->nullable();
			$table->integer('pw_error_ctr')->nullable();
			$table->boolean('login_first')->nullable();
			$table->dateTime('password_chenge_date')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_mst');
	}

}
