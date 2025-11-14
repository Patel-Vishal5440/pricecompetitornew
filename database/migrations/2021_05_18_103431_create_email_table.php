<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->string('email');
            $table->boolean('star');
            $table->text('image');
            $table->string('subject');
            $table->text('body');
            $table->time('time');
            $table->string('status');
            $table->timestamps();
        });

        // Insert some email 
        DB::table('email')->insert(
            array(
            [
                'user' => 'Alice Freeman',
                'email' => 'name@domain.com',
                'star' => false,
                'image' => 'img/author/e1.png',
                'subject' => 'Email subject lorem ipsum',
                'body' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt',
                'time' => '8:30',
                'status' => 'inbox'
            ],
            [
                'user' => 'Alice Freeman',
                'email' => 'name@domain.com',
                'star' => true,
                'image' => 'img/author/e1.png',
                'subject' => 'Email subject lorem ipsum',
                'body' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt',
                'time' => '8:30',
                'status' => 'inbox'
            ],
            [
                'user' => 'Alice Freeman',
                'email' => 'name@domain.com',
                'star' => false,
                'image' => 'img/author/e1.png',
                'subject' => 'Email subject lorem ipsum',
                'body' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt',
                'time' => '8:30',
                'status' => 'inbox'
            ],
            [
                'user' => 'Alice Freeman',
                'email' => 'name@domain.com',
                'star' => false,
                'image' => 'img/author/e1.png',
                'subject' => 'Email subject lorem ipsum',
                'body' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt',
                'time' => '8:30',
                'status' => 'inbox'
            ],
            [
                'user' => 'Alice Freeman',
                'email' => 'name@domain.com',
                'star' => true,
                'image' => 'img/author/e1.png',
                'subject' => 'Email subject lorem ipsum',
                'body' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt',
                'time' => '8:30',
                'status' => 'inbox'
            ],
            [
                'user' => 'Alice Freeman',
                'email' => 'name@domain.com',
                'star' => false,
                'image' => 'img/author/e1.png',
                'subject' => 'Email subject lorem ipsum',
                'body' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt',
                'time' => '8:30',
                'status' => 'inbox'
            ],
            [
                'user' => 'Alice Freeman',
                'email' => 'name@domain.com',
                'star' => false,
                'image' => 'img/author/e1.png',
                'subject' => 'Email subject lorem ipsum',
                'body' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt',
                'time' => '8:30',
                'status' => 'send'
            ],
            [
                'user' => 'Alice Freeman',
                'email' => 'name@domain.com',
                'star' => true,
                'image' => 'img/author/e1.png',
                'subject' => 'Email subject lorem ipsum',
                'body' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt',
                'time' => '8:30',
                'status' => 'send'
            ],
            [
                'user' => 'Alice Freeman',
                'email' => 'name@domain.com',
                'star' => false,
                'image' => 'img/author/e1.png',
                'subject' => 'Email subject lorem ipsum',
                'body' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt',
                'time' => '8:30',
                'status' => 'send'
            ])
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email');
    }
}
