<?php

//GIT Post Deploy Hook
Route::get('/git/deploy/{key}', function($key){
    if($key == env('GIT_DEPLOY_KEY')){
        return exec('/bin/bash /var/www/html/'. env('GIT_DEPLOY_FOLDER') . '/appdeploy.sh');
    }
});
