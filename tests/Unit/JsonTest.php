<?php

$enc_cred = sprintf('{"username":"admin","password":"%s"}', sha1("p@55w0rd"));
$dec_cred = array(

	"username"=>"adm",
	"password"=>sha1("p@55w0rd")
);

test("json.encode", function() use($dec_cred){

	$enc_cred = json($dec_cred)->encode();

	expect($enc_cred)->toBe($enc_cred);
});

test("json.decode", function() use($enc_cred){

	expect(json($enc_cred)->valid())->toBeTrue();

	$dec_cred = json($enc_cred)->decode();

	expect($dec_cred)->toBe($dec_cred);
});