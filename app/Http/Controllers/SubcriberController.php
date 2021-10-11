<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Subcriber;
use Validator;
Use App\Topic;


class SubcriberController extends Controller
{
    //
    public function index()
    {
        return response()->json([
            'data' => Subscriber::all()
        ]);
    }


    public function subscribe(Request $request, $topic)
    {

        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);
        if (($validator->fails()) || (!$topic) ) {
            return response()->json(['error' => $validator->errors()  || 'You did not pass any topic'], 401);
        }

        //check if topic exists
        $checkTopicExists = Topic::where('name', $topic)->count();

        if ($checkTopicExists < 1){
            return response()->json(['error' => 'The topic you selected dose not exist in out system.'], 400);
        }

        //get all subscribers

        $checkSubscriptionExists = Subcriber::where('topic', $topic)->where('url', $request->url)->count();

        if ($checkSubscriptionExists > 0){
            return response()->json([
                'message' => 'The Subscription exist in out system already thanks.',
                'url' => $request->url,
                'topic' => $topic,
            ], 200);
        }

        $subscribe = new Subcriber([
            'url' => $request->url,
            'topic' => $topic,
        ]);
        $subscribe->save();

        return response()->json([
            'url' => $request->url,
            'topic' => $topic,
        ]);
    }
}
