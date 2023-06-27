# AsyncPHP
- One Async Lib by me

# Example
```php
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

    $url1 = "https://example.com";
    $url2 = "https://google.com";

    $fiber = new Fiber(function() use ($url1, $url2) {
        $result1 = Async::await(fn() => fetchData($url1));
        var_dump($result1);
        $result2 = Async::await(fn() => fetchData($url2));
        var_dump($result2);
    });

    $fiber->start();

    Async::run();
}

test();```
