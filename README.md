# AsyncPHP
- One Async Lib by me

# Example
```php
<?php

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

Example 3:
```php
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
```
