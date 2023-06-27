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

function test() : void {

    $url = [
        "https://www.google.com",
        "https://www.bing.com",
        "https://www.yahoo.com"
    ];

    foreach ($url as $key => $value) {
        $fiber = new Fiber(function() use ($value) {
            $response = Async::await(fn() => fetchData($value));
            echo $response . PHP_EOL;
        });
        $fiber->start();
    }

    Async::run();
}

test();
