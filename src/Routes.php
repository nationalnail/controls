<?php

use Illuminate\Http\Request;

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

//Contact Form Routes
Route::get('/contact-us', function(){
    $locationdata = NNC::gmap_nnc_locations();
    return view('company.contact', ['locations' => $locationdata]);
})->name('Contact Us');

Route::post('/contact/locations', function(){
    return DB::table('locations')->where('active', 1)->get();
});

Route::post('/contact-us/submit', function(){
        $name = $request->input('name');
        $phone = $request->input('phone');
        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $email = $request->input('email');
        $company = $request->input('company');
        $about = $request->input('about');
        $message = $request->input('message');
        $recaptcha = $request->input('g-recaptcha-response');
        if ($request->isMethod('POST')){
            $gcapverify = NNC::recaptcha_verification($recaptcha);
            $emails = NNC::engine_data_email_system(env('GIT_DEPLOY_FOLDER'), 'contact');

            if($gcapverify == true){
                $data = [
                    'subject'=> 'Contact Form Submission',
                    'phone' => $phone,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip,
                    'company' => $company,
                    'about' => $about,
                    'email' => $email,
                    'name' => $name,
                    'message'=> $message,
                    'button' => 'Respond',
                    'type' => 'Contact Form'
                ];

                Mail::to($emails)->send(new ContactUs($data));
                return response()->json(['success' => 'true', 'message' => '<div class="alert alert-success"><i class="fa fa-check fa-2x success"></i>  Your message was successfully sent!</div>']);
            }else{
                return response()->json(['success' => 'false', 'message' => '<div class="alert alert-danger">Your message failed, please verify you are not a robot and try again.</div>']);
            }
        }else{
            return response()->json(['success' => 'false', 'message' => '<div class="alert alert-danger">Your message failed, please check your text and try again.</div>']);
        }
        return 'Total Failure';
});

//Dealer Locator Routes
Route::get('/locator', function(){
    return view('company.locator');
})->name('Dealer Locator');

Route::post('/locator/getdata', function(Request $request){
    $app_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $address = $request->input('params.address');
    $radius = $request->input('params.radius');
    $brand = $request->input('params.brand');
    if ($request->isMethod('POST')){
        $coords = NNC::gmap_rgeolocation($address);

        $state = $coords['state'];
        $country = $coords['country'];
        DB::connection('dealerlocator')->table('dealers_meta')->insert(
            [
                'created_date' => date("Y-m-d H:i:s"),
                'app' => $app_url,
                'address' => str_replace('+', ' ', $address),
                'state' => $state,
                'country' => $country,
                'radius' => $radius,
                'brand' => $brand,
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
            ]
        );

        if($brand == 'All Brands'){
            $query = sprintf("SELECT *, ( 3959 * acos( cos( radians('%s') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude ) ) ) ) AS distance FROM dealers WHERE active = 1 HAVING distance < %s ORDER BY distance",
            $coords['lat'], $coords['lng'], $coords['lat'], $radius);
            $data = DB::connection('dealerlocator')->select($query);
            return $data;
        }else{
            $query = sprintf("SELECT *, ( 3959 * acos( cos( radians('%s') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude ) ) ) ) AS distance FROM dealers WHERE active = 1 AND %s = 1 HAVING distance < '%s' ORDER BY distance",
            $coords['lat'], $coords['lng'], $coords['lat'], strtolower($brand), $radius);
            $data = DB::connection('dealerlocator')->select($query);
            return $data;
        }
    }
});

if (App::environment('qa')){
    Route::get('/locator/geoupdate', function(Request $request){
        ini_set('max_execution_time', '1000');
        $query = DB::table('geolocation')->get();
        foreach($query as $db){
            $address_1 = $db->address_1;
            $address_2 = $db->address_2;
            $city = $db->city;
            $state = $db->state;
            $zip = $db->zip;
            $id = $db->id;

            $address = $address_1 . $address_2 . $city . $state . $zip;

            $coords = NNC::gmap_rgeolocation($address);

            DB::connection('dealerlocator')->table('geolocation')->where('id', $id)->update(['latitude' => $coords['lat'], 'longitude' => $coords['lng']]);
        }
        return 'Rows Updated';
    });
}