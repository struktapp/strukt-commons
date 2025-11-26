<?php

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

test('arr.contains', function () use($arr){

    expect($arr->contains("Johnliver"))->toBeTrue();
});

test("arr.filter", function(){

    expect(arr(["A","B", null])->filter()->yield())->toBe(["A","B"]);
    expect(arr([

        "username"=>"pitsolu",
        "password"=>"p@55w0rd",
        "role"=>null

    ])->filter()->yield())->toBe([

        "username"=>"pitsolu",
        "password"=>"p@55w0rd",
    ]);

    expect(arr([

        "username"=>"pitsolu",
        "password"=>"p@55w0rd",
        "role"=>null

    ])->filter(fn($k,$v)=>$k=="password")->yield())->toBe([

        "username"=>"pitsolu",
        "role"=>null
    ]);
});

test("arr[iterator]", function() use($arr){

    expect($arr->current())->toBe("Sander Wellington");
    expect($arr->next())->toBeTrue();
    expect($arr->current())->toBe("Johnliver");
    $arr->last();
    expect($arr->key())->toBe("contact");
    expect($arr->next())->toBeFalse();
    $arr->reset();
    expect($arr->current())->toBe("Sander Wellington");
    expect($arr->key())->toBe("othernames");
    $arr->next();
    $arr->next();
    $arr->next();
    expect($arr->next())->toBeFalse();
    expect($arr->valid())->toBeFalse();
});

test("arr.each", function(){

    $arr = arr([

        "first_name"=>"Peter",
        "second_name"=>"Pan",
        "last_name"=>"Joe"
    ]);

    $arr = $arr->each(fn($k, $v)=>$k=="last_name"?"Dennis":$v);
    $arr = $arr->skip("second_name")->each(fn($k, $v)=>$v."...");
    $arr = $arr->jump("Peter")->each(fn($k,$v)=>$v."+++");
    $arr = $arr->stop("second_name")->each(fn($k,$v)=>$v."---");

    expect($arr->first())->toBe("Peter...+++---");
    $arr->next();
    expect($arr->current())->toBe("Pan+++");
    expect($arr->last())->toBe("Dennis...+++");
});

test("arr.level", function() use($arr){

    $leveled = [
      "othernames" => "Sander Wellington",
      "surname" => "Johnliver",
      "contact.mobile" => "+254 712 788 999",
      "contact.address.home" => "Westminiser, Long Street, 453, Middlearth",
      "contact.address.office" => "Dayriyon, Quadtratic Solusis"
    ];

    expect($arr->level())->toBe($leveled);
});

test("arr.column", function(){

    $users = [
        ["username"=>"pitsolu","type"=>"admin"],
        ["username"=>"peterparker","type"=>"user"],
        ["username"=>"ludivar","type"=>"user"]
    ];

    $usernames = arr($users)->column("username")->yield();
    foreach($users as $user)
        expect(in_array($user["username"], $usernames))->toBeTrue();
});

test("arr.enqueue", function() use($arr){

    $arr = $arr->enqueue("wellsander", "username");//Key is optional
    $username = $arr->last();
    expect($username)->toBe("wellsander");
});

test("arr.prequeue", function() use($arr){

    $arr = $arr->prequeue("administrator", "type");//Key is optional
    $arr->reset();
    $type = $arr->current();
    expect($type)->toBe("administrator");
});

test("arr.dequeue", function() use($arr){

    $othernames = $arr->dequeue();
    expect($othernames)->toBe("Sander Wellington");
    expect($arr->has("othernames"))->toBeFalse();
});

test("arr.pop", function() use($arr){

    $contacts = $arr->pop();
    expect(array_key_exists("mobile", $contacts))->toBeTrue();
    expect($arr->has("contacts"))->toBeFalse();
});

test("arr.push", function() use($arr){

    $arr = $arr->push("Active", "status");//Key is optional
    $status = $arr->last();
    expect($status)->toBe("Active");
});