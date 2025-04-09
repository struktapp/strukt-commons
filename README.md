Strukt Commons
==============

[![Build Status](https://travis-ci.org/pitsolu/strukt-commons.svg?branch=master)](https://packagist.org/packages/strukt/commons)
[![Latest Stable Version](https://poser.pugx.org/strukt/commons/v/stable)](https://packagist.org/packages/strukt/commons)
[![Total Downloads](https://poser.pugx.org/strukt/commons/downloads)](https://packagist.org/packages/strukt/commons)
[![Latest Unstable Version](https://poser.pugx.org/strukt/commons/v/unstable)](https://packagist.org/packages/strukt/commons)
[![License](https://poser.pugx.org/strukt/commons/license)](https://packagist.org/packages/strukt/commons)

## Usage

### Collection

```php
$contact = collect([]);
$contact->set("mobile", "+2540770123456");
$contact->set("work-phone", "+2540202345678");

$user = collect([]);
$user->set("contacts", $contact);
$user->get("contacts.mobile"); //outputs +2540770123456

$s = array(
    "user"=>array(
        "firstname"=>"Gene",
		"surname"=>"Wilder",	
		"db"=>array(
            "config"=>array(
                "username"=>"root",
				"password"=>"_root!"
            )
        ),
        "mobile_numbers"=>array( //Non Assoc Array
            "777111222",
            "770234567"
        )
    )
);

$b = collect($s);
$x = $b->get("user.db.config.username"); //returns root
```

## Value Objects

You may also find `Number` object via package `strukt/math` that is a dependency of this package.

### DateTime

```php
$start = when()
$end = when("+30 days");
$rand = $start->rand($end);//make start date random date between start and end
$end->gt($start);//true
$end->gte($start);//true
$start->lt($end);
$end->lte($end);//true
$start->equals($end);//false
$start->same(new DateTime);//true -- is the same day
$newStart = $start->clone();
$newStartPlusOneDay = $start->clone("+1 day");
$start->reset();//reset time to 00:00:00 000000
$start->last();//reset time to 23:59:59 1000000
echo $start; //return date as string
```

### Today (Date Influence)

```php
// $p = period(when("1900-01-01"), when("1963-12-31")); 
$p = period()

//In order for date influence to work the first 2 line below must be 
// called before any further date manipulation
// messing arround with dates can create headaches
$p->create(when("1900-01-01"), when("1963-12-31")); //period
$p->reset(when("1960-03-23"));//create fake today
$fakeToday = today();

//All dates created with Strukt\Type\DateTime will be in 1960
$fakeToday->same(new DateTime); //false
$fakeToday->same(when());//true
$fakeToday->hasPeriod()//true -- has period
$fakeToday->withDate(when("1959-04-01"))->isValid(); //true -- is date valid with period

// $fakeToday->getState("period.start");
// $fakeToday->getState("period.end");
$fakeToday->getState();//get state of date manipulation
$fakeToday->reset();//reset back to original today
```

### String

```php
$str = str("Strukt Framework");
$str->empty();//false
$str->startsWith("Str");//true
$str->endsWith("work");//true
$str->first(3);//Str
$str->last(4);//work
$str->contains("Frame");//true
$str->slice(7,5)->equals("Frame");//true
$str->replace("work", "play")->equals("Strukt Frameplay");
$str->replaceFirst("k","c")->equals("Struct Framework");
$str->replaceLast("k","d")->equals("Struct Frameword");
$str->replaceAt("ing", 3, 3)->equals("String Framwork")
$str->toUpper();//STRUKT FRAMEWORK
$str->toLower();//strukt framework
$camel = str("thisIsCamelCase");
$camel->toSnake();//this_is_camel_case
$camel->toSnake()->toCamel();//ThisIsCamelCase
$sdo = $str->prepend("Doctrine + ");//Doctrine + Strukt Framework
$sdo->concat(" = Strukt Do");//Doctrine + Strukt Framework = Strukt Do
$str->split(" ");//['Strukt', "Framework"]
str("blah blah blah")->count("blah");//3
```

### Array

```php
$rr = array(
    "firstname"=>"Bruce",
    "lastname"=>"Wayne",
    "alias"=>"Joker",
    "contacts" =>array(
        "email"=>"brucewayne@wayneent.com",
        "address"=>array(

            "street"=>"Boulavard of Broken Dreams",
            "building"=>"Wayne Co."
        )
    )
);

$arr = arr($rr);//Arr::create($rr)
$arr->has("Banner")//false
$arr->empty();//false
$arr->length();//3
$arr->count();//3
$arr->next();//true
$arr->current()->yield();//Wayne
$arr->key();//lastname
$arr->last()->equals($rr["contacts"]);
$arr->reset();
$arr = $arr->each(function($key, $val){ //loop

    if($key == "alias")
        $val = "Batman";

    return $val;
});
$arr = $arr->recur(function($key, $val){ //recursive iterate 

    if($key == "building")
        $val = "Wayne Co. & Associates";

    return $val;
});
$origarr = $arr->yield();
$rawarr = $arr->map(array( //reformat array

    "email_contact"=>"contacts.email",
    "address_street"=>"contacts.address.street",
    "address_building"=>"contacts.address.building"
));
$arr->pop();// remove at end of array.
$arr->push("brucebatman", "username");//add at end of queue. (key is optional)
$arr->enqueue("active", "status");//same as Arr.push (key is optional)
$arr->enqueueBatch(["empty","empty"]);
$arr->prequeue("admin", "type");//add at beginning of queue. (key is optional)
$arr->dequeue();//remove at beginning of array. returns Bruce
$flatarr = $arr->level();//flattens multidimentional array
$is_assoc = arr(["username"=>"pitsolu", "password"="redacted"])->isMap();//is fully associative arr
$arr = arr(array(
    array(
        "username"=>"pitsolu",
        "type"=>"admin"
    ),
    array(
        "username"=>"peterparker",
        "type"=>"user"
    )
));
$arr->column("type");//returns array("admin", "user")
$arr = arr(array(

    "user"=>"pitsolu",
    "type"=>"admin",
    "status"=>"active"
));
$arr->tokenize();//returns user:pitsolu|type:admin|status:active
$arr->concat(",")//pitsolu,admin,active
arr(["a","b","c"])->isOfStr();// true
arr([1,2,3])->isOfNum();// true 
arr(["a","a","b","b","b","c","c","d"])->distict()->yield();//["a" => 2,"b" => 3,"c" => 2,"d" => 1]
arr(["a","b","b","c","c","c","d"])->uniq()->yield();//["a","b","c","d"]
arr(["a","b","c","d"])->slice(2)//["c","d"]
arr(["a","b","c","d"])->slice(1,3)//["b","c","d"]

$x = ["name"=>"peter","email"=>"peter@gmail.com"];
arr($x)->only(["email"])->yield()//["email"=>"peter@gmail.com"]

$x = [["name"=>"peter","email"=>"peter@gmail.com"], ["name"=>"john","email"=>"john@gmail.com"]]
arr($x)->order()->asc("email")->yield()//sorting 2d array by column
arr($x)->nested();//is nested array - true
arr([1,2,3])->product();//6
arr(["ab","cd","ef"])->has("ab");//true
arr(["a"=>1,"b"=>2,"c"=>3])->contains("a")//true
arr(["a"=>1,"b"=>2,"c"=>3])->values()//[1,2,3]
arr(["a","b"])->merge(["c","d"])->yield();//["a","b","c","d"]
arr(["a","b","c","d"])->reverse()->yield();//["d","c","b","a"]
```
## Others

### Token Query

```php
/**
 * Basic Token 
 */
$token = "user:pitsolu|status:active|is_superuser:true";

$query = token($token);

$query->get("user");//pitsolu
$query->get("status");//active
$query->get("is_superuser");//true

$query->has("role");//false
$query->keys();//["user","status","is_superuser"]

$query->token();//original token -- user:pitsolu|status:active|is_superuser:true
$query->set("role","admin");
$query->yield();//user:pitsolu|status:active|is_superuser:true|role:admin

/**
 * Complex Token
 */
$token = "contact:1|is:tenant,landlord,prospect";

$query = token($token);

$query->get("is");//["tenant","landlord","prospect"];
$query->set("status", ["active","published"]);
$query->yield();//contact:1|is:tenant,landlord,prospect|status:active,published
```

### Heap/Messages

```php
heap("error 401!");
heap("error 402!");
heap("error 404!");

# $errors = heap()->get(pattern:"error");
$errors = heap()->get();

$errors->last()->yield(); //error 404!
$errors->reset();
$errors->current()->yield(); //error 401!
$errors->next();
$errors->current()->yield(); //error 402!
```

### Json

```php
$l = json(array("fname"=>"Peter", "lname"=>"Pan"));//json string
$l->encode();//json string
$m = $l->pp();//pretty print
$n = $l->decode();//array
json("{}")->valid();// is valid json. will return true
```