<?php

require_once __DIR__ . "/vendor/autoload.php";

use vennv\async\Async;

function fetchData($url) : mixed {
    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($curl);

    if (!$response) {
        $error = curl_error($curl);
        curl_close($curl);
        return "Error: " . $error;
    }

    curl_close($curl);

    return $response;
}

function test() : Async { 
    return Async::create(function() {

        $url = [
            "https://www.google.com",
            "https://www.youtube.com"
        ];

        $responses = [];
        
        foreach ($url as $value) {
            $responses[] = Async::await(fn() => fetchData($value));
        }

        return $responses;
    });
}

test()->fThen([
    "success" => function($value) {
        array_map(function ($v) {
            echo $v . PHP_EOL;
        }, $value);
    },
    "error" => function($error) {
        echo $error . PHP_EOL;
    }
]);