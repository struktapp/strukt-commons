<?php

require "vendor/autoload.php";

$arr = arr([
    "othernames" => "Sander Wellington",
    "surname" => "Johnliver",
    "contact" => [
        "mobile"=>"+254 712 788 999",
        "address"=>[
            "home"=>"Westminiser, Long Street, 453, Middlearth",
            "office"=>"Dayriyon, Quadtratic Solusis"
        ]
    ]
]);

dd($arr->contains("Johnliverx"));