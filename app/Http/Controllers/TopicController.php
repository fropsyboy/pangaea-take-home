<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Topic;
use Validator;
use Illuminate\Support\Facades\Http;
Use App\Subcriber;

class TopicController extends Controller
{
    //
    public function index()
    {
        return response()->json([
            'data' => Topic::all()
        ]);
    }

    public function publish(Request $request, $topic)
    {

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);
        if (($validator->fails()) || (!$topic) ) {
            return response()->json(['error' => $validator->errors()  || 'You did not pass any topic'], 401);
        }

        // save the new message
        $newTopic = new Topic([
            'name' => $topic,
            'message' => $request->message,
        ]);
        $newTopic->save();

        //get all subscribers
        $getAllSubscription = Subcriber::where('topic', $topic)->get();

        if (count($getAllSubscription) < 1){

            return response()->json([
                'message' => 'There are no subscribers for this topic',
                'url' => $request->url,
                'topic' => $topic,
            ], 200);
        }

        $faildPublished = [];
        $successfulPublished = [];

        foreach ($getAllSubscription as $subscriber) {

            try {

                $response = Http::post($subscriber->url, [
                    'topic' => $topic,
                    'data' => [
                        "message" => $request->message
                    ],
                ]);

                if ( $response->ok() === true){
                    array_push($successfulPublished, $subscriber->url);
                }else{
                    array_push($faildPublished, $subscriber->url);
                }

                
              
              } catch (\Exception $e) {

                array_push($faildPublished, $subscriber->url);
                  
              }

        }


        return response()->json([
            'topic' => $topic,
            'data' => [
                "message" => $request->message
            ],
            'successful push to subcribers' => $successfulPublished,
            'failed push to subcribers' => $faildPublished,
        ]);
    }
}
