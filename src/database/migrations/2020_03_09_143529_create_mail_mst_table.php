<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMailMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mail_mst', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('mail_id');
			$table->string('mail_remark');
			$table->timestamps();
			$table->string('mail_ma_name')->nullable()->comment('管理名称');
			$table->string('mail_text')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mail_mst');
	}

}
