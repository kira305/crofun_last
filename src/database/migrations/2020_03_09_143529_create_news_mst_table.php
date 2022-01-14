<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNewsMstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('news_mst', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('infor_key');
			$table->string('infor_title');
			$table->string('infor_content');
			$table->integer('company_id');
			$table->integer('role_id');
			$table->timestamp('published_strt')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('published_end')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->boolean('delete_flg');
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
		Schema::drop('news_mst');
	}

}
