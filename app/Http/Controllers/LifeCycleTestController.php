<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LifeCycleTestController extends Controller
{
    //
    public function showeServiceContainerTest() 
    {
        app()->bind('lifeCycleTest',function(){
            return 'ライフサイクルテスト';
        });

        $test = app()->make('lifeCycleTest');

        //サービスコンテナなしの場合
        // $message = new Message();
        // $sample= new sample($message);
        // $sample->run();

        //サービスコンテナapp()ありの場合
        app()->bind('sample',Sample::class);
        $sample = app()->make('sample');
        $sample->run();

        dd($test, app());
    }
}

class Sample 
{
    public $message;
    public function __construct(Message $message) {
        $this->message = $message;
    }
    public function run() {
        $this->message->send();
    }
}

class Message
{
    public function send(){
        echo('メッセージ表示');
    }
}