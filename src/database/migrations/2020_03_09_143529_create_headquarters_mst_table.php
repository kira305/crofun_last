<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHeadquartersMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('headquarters_mst', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('headquarters');
			$table->integer('company_id');
			$table->string('headquarter_list_code');
			$table->string('note')->nullable();
			$table->boolean('status');
			$table->timestamps();
			$table->integer('headquarters_code')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('headquarters_mst');
	}

}
