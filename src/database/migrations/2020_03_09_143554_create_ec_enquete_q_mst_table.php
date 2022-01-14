<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEcEnqueteQMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ec_enquete_q_mst', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('client_id');
			$table->string('upd_client_name');
			$table->string('client_name_abbreviated');
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
		Schema::drop('ec_enquete_q_mst');
	}

}
