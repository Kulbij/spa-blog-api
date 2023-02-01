<?php

use App\Models\Blog\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Post::getTableName(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();

            $table->string('image')->nullable();

            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_enabled')->default(false)->index();
            
            $table->softDeletes();
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
        Schema::dropIfExists(Post::getTableName());
    }
}
