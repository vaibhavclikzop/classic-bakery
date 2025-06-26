<?php

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

if (!function_exists('CheckUserPassword')) {
    function CheckUserPassword($param)
    {
        $users =  DB::table("users")->where("password", $param)->first();
        if ($users) {
            return $users;
        } else {
            return false;
        }
    }
}

 