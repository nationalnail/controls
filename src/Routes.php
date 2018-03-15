<?php

//GIT Post Deploy Hook
Route::get('/git/deploy/{key}', function($key){
    if($key == env('GIT_DEPLOY_KEY')){
        return exec('/bin/bash /var/www/html/'. env('GIT_DEPLOY_FOLDER') . '/appdeploy.sh');
    }
});

//Feedback Control POST
Route::post('/feedback/submit', function(){
    $location = $request->input('class');
    $response = $request->input('response');
    if($response == 'thumbs-up'){
        $response = 1;
    }elseif ($response == 'thumbs-down') {
        $response = 0;
    }
    if ($request->isMethod('POST')){
        DB::connection('engine')->table('feedback')->insert(
            [
                'app' => env('GIT_DEPLOY_FOLDER'),
                'created_date' => date("Y-m-d H:i:s"),
                'feedback_page' => $location,
                'response' => $response,
            ]
        );
        return response()->json(['success' => 'true']);
    }
    return response()->json(['success' => 'false']);
});