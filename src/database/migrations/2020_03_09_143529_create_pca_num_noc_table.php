<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePcaNumNocTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pca_num_noc', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('client_id');
			$table->integer('company_id');
			$table->string('pca_noc_code');
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
		Schema::drop('pca_num_noc');
	}

}
