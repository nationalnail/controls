<?php

namespace NNC\Controls;

use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;

class NNCControlsController extends Seeder
{
    //Alert System Function
    public function alert_system(){
        $data = DB::connection('engine')->table('alert_system')
            ->where('app', '=', env('GIT_DEPLOY_FOLDER'))
            ->where('active', 1)
            ->first();
        if(!empty($data)){
            if($data->end > date('Y-m-d H:i:s')){
                $data->active = 'true';
                $output = '<div class="alert-block fixed-top" data-active="' . $data->active . '">';
                    $output .= '<div class="alert-' . $data->type . ' bg-' . $data->color . ' pt-3 pb-4">';
                    $output .= '<p class="text-center font-weight-bold text-capitalize text-white p-0 m-0">' . $data->title . '</p>';
                $output .= '</div></div>';
                return $output;
            }elseif($data->end < date('Y-m-d H:i:s')){
                DB::connection('engine')->table('alert_system')->where('id', '=', $data->id)->update([
                    'active' => 0
                ]);
            }
        }
    }

    //Internet Connection Function
    public function connection_system(){
        $connected = @fsockopen("www.google.com", 80);
        //website, port  (try 80 or 443)
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }

    //Engine Data Functions
    public function engine_data_states(){
        return DB::connection('engine')->table('states')->where('active', 1)->get();
    }

    //AWS S3/CoudFront Functions
    public function aws_get_files(){
        return Storage::disk('s3')->allFiles();
    }

    //Careers Control
    public function carrers_jobs(){
        return DB::table('careers')->where('active', 1)->get();
    }

    //Carousel Control
    public function carousel_slides(){
        return DB::table('slider')->where('active', 1)->get();
    }

    public function carousel_testimonials(){
        return DB::table('testimonials')->where('active', 1)->get();
    }

    //Legal Control
    public function legal_tos(){
        return DB::connection('engine')->table('legal')->where('type', 'tos')->where('app', env('GIT_DEPLOY_FOLDER'))->where('active', 1)->get();
    }

    public function legal_pri(){
        return DB::connection('engine')->table('legal')->where('type', 'pri')->where('app', env('GIT_DEPLOY_FOLDER'))->where('active', 1)->get();
    }

    //News Control
    public function news_ads(){
        return DB::table('ads')->where('active', 1)->get();
    }

    public function news_articles(){
        return DB::table('articles')->where('active', 1)->get();
    }

    public function news_pressreleases(){
        return DB::table('pressreleases')->where('active', 1)->get();
    }

    //GMaps Control
    public function gmap_nnc_locations(){
        return DB::table('locations')->where('active', 1)->get();
    }

    public function gmap_rgeolocation($address){
        $gaddress = str_replace(' ', '+', $address);
        $google_geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?' . 'address=' . $gaddress . '&key=' . env('GOOGLE_MAPS_API_KEY');
        $json = file_get_contents($google_geocode_url);
        $geocode_data = json_decode($json, TRUE);

        $lat = '';
        $lng = '';
        $location = array();
        $location['street_number'] = '';
        $location['street'] = '';
        $location['locality'] = '';
        $location['county'] = '';
        $location['state'] = '';
        $location['postal_code'] = '';
        $location['country'] = '';
        if($geocode_data['status']=="OK"){
            $lat .= $geocode_data['results']['0']['geometry']['location']['lat'];
            $lng .= $geocode_data['results']['0']['geometry']['location']['lng'];
            foreach ($geocode_data['results']['0']['address_components'] as $component) {
                switch ($component['types']) {
                case in_array('street_number', $component['types']):
                    $location['street_number'] = $component['short_name'];
                    break;
                case in_array('route', $component['types']):
                    $location['street'] = $component['short_name'];
                    break;
                case in_array('locality', $component['types']):
                    $location['locality'] = $component['short_name'];
                    break;
                case in_array('administrative_area_level_2', $component['types']):
                    $location['county'] = $component['long_name'];
                    break;
                case in_array('administrative_area_level_1', $component['types']):
                    $location['state'] = $component['short_name'];
                    break;
                case in_array('postal_code', $component['types']):
                    $location['postal_code'] = $component['short_name'];
                    break;
                case in_array('country', $component['types']):
                    $location['country'] = $component['long_name'];
                    break;
                }
            }
        }

        $data = [
            'lat' => $lat,
            'lng' => $lng,
            'street_number' => $location['street_number'],
            'street' => $location['street'],
            'address_1' => $location['street_number'] . ' ' . $location['street'],
            'city' => $location['locality'],
            'county' => $location['county'],
            'state' => $location['state'],
            'zip' => $location['postal_code'],
            'country' => $location['country'],
            'geocodeurl' => $google_geocode_url,
            'geocodedata' => $geocode_data
        ];
        return $data;
    }

    //Captcha Control
    public function recaptcha_js(){
        return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
    }

    public function recaptcha_init(){
        return '<div class="g-recaptcha" data-sitekey="' . env('GOOGLE_RECAPTCHA_SITE_KEY') . '"></div>';
    }

    public function recaptcha_verification($recaptcha){
        $google_recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify?' . 'response=' . $recaptcha . '&secret=' . env('GOOGLE_RECAPTCHA_SECRET_KEY');

        $json = file_get_contents($google_recaptcha_url);
        $recaptcha_data = json_decode($json, TRUE);

        return $recaptcha_data['success'];
    }
}
