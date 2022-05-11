<?php

use Strukt\Monad;
use Strukt\Type\Str;
use Strukt\Type\Arr;

class MonadTest extends PHPUnit\Framework\TestCase{

	public function testRulesExample(){

		// $rules = "is:general|contra:xx-xx";
		// $rules = "contra:yy-yy-yy";
		$rules = "has:withholding-tax@0.2|contra:blah-blah";

		$ruleset = Monad::create([$rules])
			->next(function($rules){

				return Str::create($rules)->split("|");
			})
			->next(function($rules){

				$rules = Arr::create($rules)->each(function($key, $rule){

					$rule = Str::create($rule);

					if($rule->contains("@")){

						list($cond, $val) = $rule->split("@");
						list($stmt, $param) = Str::create($cond)->split(":");

						$rule = Str::create($param)->concat(":")->concat($val)->yield();

						return [$cond, $rule];
					}

					return $rule->yield();
				});

				return array_values(Arr::level($rules->yield()));
			});

		$this->assertEquals($ruleset->yield(), array(

		    "has:withholding-tax",
		    "withholding-tax:0.2",
		    "contra:blah-blah",
		));
	}
}
