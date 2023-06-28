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

    Async::create(function() {

        $url = [
            "https://www.google.com",
            "https://www.youtube.com"
        ];
        
        foreach ($url as $value) {
            //throw new \Exception("Error");
            $response = Async::await(fn() => fetchData($value));
            echo $response . PHP_EOL;
        }
        
    })->fThen([
        "success" => function($value) {
            echo $value . PHP_EOL;
        },
        "error" => function($error) {
            echo $error . PHP_EOL;
        }
    ]);
}

test();
```
- Example 2:
```php
function test() : Async { 
    return Async::create(function() {

        $url = [
            "https://www.google.com",
            "https://www.youtube.com"
        ];
        
        foreach ($url as $value) {
            //throw new \Exception("Error");
            //$response = Async::await(fn() => fetchData($value));
            //echo $response . PHP_EOL;
        }

        return "Done";

    });
}

test()->fThen([
    "success" => function($value) {
        echo $value . PHP_EOL;
    },
    "error" => function($error) {
        echo $error . PHP_EOL;
    }
]);
```
