<?php

$c = collect([
	"username" => "genewilder",
    // "othernames" => "Gene Wilder",
    "surname" => "Smith",
    "contact" => [
        "mobile"=>"+254 712 788 999",
        "address"=>[
            "home"=>"Westminiser, Long Street, 453, Middlearth",
            "office"=>"Dayriyon, Quadtratic Solusis"
        ]
    ]
]);

test("collect.set", function() use($c){

	expect($c->exists("othernames"))->toBeFalse();
	$c->set("othernames", "Gene Wilder");
	expect($c->exists("othernames"))->toBeTrue();
});

test("collect.get", function() use($c){

	expect($c->get("othernames"))->toBe("Gene Wilder");
});

test("collect.keys", function() use($c){

	expect(collect($c->get("contact.address"))->keys())->toBe(["home","office"]);
});

test("collect.get[nested]", function() use($c){

	expect($c->get("contact.mobile"))->toBe("+254 712 788 999");
});

test("collect.remove", function() use($c){

	$c->remove("surname");
	
	expect($c->exists("surname"))->toBeFalse();
});

test("collect.ask", function() use($c){

	$a = $c->ask("contact.address");
	expect($a instanceof Strukt\Collection)->toBeTrue();
});
