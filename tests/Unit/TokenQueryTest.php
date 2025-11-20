<?php

test("token[basic]", function(){

	$token = "user:pitsolu|status:active|is_superuser:true";
	$query = token($token);

	expect($query->get("user"))->toBe("pitsolu");
	expect($query->get("status"))->toBe("active");
	expect($query->get("is_superuser"))->toBe("true");
	expect($query->has("role"))->toBe(false);
	expect($query->keys())->toBe([

		"user",
		"status",
		"is_superuser"
	]);

	$query->set("role","admin");
	$token = sprintf("%s|role:admin", $query->token());
	expect($query->yield())->toBe($token);
});

test("token[complex]", function(){

	$token = "contact:1|is:tenant,landlord,prospect";
	$query = token($token);
	expect($query->get("is"))->toBe([

		"tenant",
		"landlord",
		"prospect"
	]);

	expect($query->yield())->toBe($token);

	$query->set("status", ["active","published"]);
	$token = sprintf("%s|status:active,published", $token);
	expect($query->yield())->toBe($token);
});

test("tokenize", function(){

	$token = "user:pitsolu|status:active|permissions:user_add,user_update";
	$new_token = tokenize([
		"user"=>"pitsolu",
		"status"=>"active",
		"permissions"=>[
			"user_add",
			"user_update"
		]
	]);

	expect($token)->toBe($new_token);
});