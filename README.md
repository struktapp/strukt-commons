Strukt Commons
==============

[![Build Status](https://travis-ci.org/pitsolu/strukt-commons.svg?branch=master)](https://packagist.org/packages/strukt/commons)
[![Latest Stable Version](https://poser.pugx.org/strukt/commons/v/stable)](https://packagist.org/packages/strukt/commons)
[![Total Downloads](https://poser.pugx.org/strukt/commons/downloads)](https://packagist.org/packages/strukt/commons)
[![Latest Unstable Version](https://poser.pugx.org/strukt/commons/v/unstable)](https://packagist.org/packages/strukt/commons)
[![License](https://poser.pugx.org/strukt/commons/license)](https://packagist.org/packages/strukt/commons)

# Usage

## Collection

```php
//use Strukt\Core\Collection
$contact = new Collection("Contacts");
$contact->set("mobile", "+2540770123456");
$contact->set("work-phone", "+2540202345678");

$user = new Collection("User");
$user->set("contacts", $contact);
$user->get("contacts.mobile"); //outputs +2540770123456
```

## Collection Builder

```php
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
//use Strukt\Builder\Collection as CollectionBuilder
//use Strukt\Core\Collection
// $x = CollectionBuilder::create(new Collection())->fromAssoc($s);
// $b = new CollectionBuilder(new Collection());
$b = new CollectionBuilder();
$x = $b->fromAssoc($s); //returns \Collection
```

## Map

```php
//use Strukt\Core\Map
//use Strukt\Core\Collection
$map = new Map(new Collection());
$map->set("session.user.username", "genewilder");
$map->set("session.user.firstname", "Gene");
$map->set("session.user.surname", "Wilder");
$map->set("db.config.username", "root");
$map->set("db.config.password", "_root");
```

Both `Map` and `Collection` have functions `set` , `get` , `exist` , `remove` The difference between both utilities is that `Map` can `set` and `remove` deep values while `Collection` cannot.

## Events

```php
//use Strukt\Event
$credentials = array("username"=>"admin", "password"=>"p@55w0rd");

$login = Event::create(function($username, $password) use($credentials){

    return $username == $credentials["username"] && $password == $credentials["password"];
});

$isLoggedIn = $login->apply("admin","p@55w0rd")->exec();
// $isLoggedIn = $login->applyArgs($credentials)->exec();
```

# Value Objects

## Number

```php
use Strukt\Type\Number;

$num = new Number(1000);
$num = $num->add(200);//1200
$num = $num->subtract(100);//1100
$num = $num->times(2);//2200 multiplication
$num = $num->parts(4);//550 division
$rem = $num->mod(9);//1 modulus
$num = $num->raise(2);//302500 power
list($num1, $num2) = $num->ratio(1,1);//151250, 151250
list($num1, $num2) = $num->ratio(1,3);//75625,226875
list($num1, $num2, $num3) = $num->ratio(1,1,3);//60500,60500,181500
$num->gt(302499);//true; greaterthan
$num->gte(302500);//true greaterthanorequals
$num->lt(302499);//false lessthan
$num->lte(302501);//true lessthanorequals
$num->negate()->equals(-302500)  
$num->yield();//return native number
$num->reset();//0
Number::random(4, 10, 20); //return 4 random numbers between 10 and 20
Number::create(10.1)->type();//double
echo $num;//return native number
```

## DateTime

```php
use Strukt\Type\DateTime;//inherits native \DateTime object

//DateTime::create("10-31-2010", "m-d-Y")
//DateTime::fromTimestamp(1198998987)
$start = new DateTime();
$end = new DateTime("+30 days");
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

## Today (Date Influence)

```php
use Strukt\Core\Today;
// use Strukt\Type\DateTime;

//In order for date influence to work the first 2 line below must be 
// called before any further date manipulation
// messing arround with dates can create headaches
Today::validBtwn(new DateTime("1900-01-01"), new DateTime("1963-12-31")); //period
Today::reset(new DateTime("1960-03-23"));//create fake today
$fakeToday = new Today();

//All dates created with Strukt\Type\DateTime will be in 1960
$fakeToday->same(new DateTime); //false
$fakeToday->same(new Strukt\Type\DateTime);//true
Today::hasRange()//true -- has period
$fakeToday->withDate(new DateTime("1959-04-01"))->useRange()->isValid()); //true -- is date valid with period

Today::getState();//get state of date manipulation
Today::reset();//reset back to original today
```

## String

```php
use Strukt\Type\Str;

$str = new Str("Strukt Framework");
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
$camel = new Str("thisIsCamelCase");
$camel->toSnake();//this_is_camel_case
$camel->toSnake()->toCamel();//ThisIsCamelCase
$sdo = $str->prepend("Doctrine + ");//Doctrine + Strukt Framework
$sdo->concat(" = Strukt Do");//Doctrine + Strukt Framework = Strukt Do
$str->split(" ");//['Strukt', "Framework"]
(new Str("blah blah blah"))->count("blah");//3
```

## Array

```php
use Strukt\Type\Arr;

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

$arr = new Arr($rr);//Arr::create($rr)
$arr->has("Banner")//false
$arr->empty();//false
$arr->length();//3
$arr->only(3);//true
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
$flatarr = Arr::level($rr);//flattens multidimentional array
$is_assoc = Arr::isMap(["username"=>"pitsolu", "password"="redacted"]);//is fully associative arr
```
# Others

## Token Query

```php
/**
 * Basic Token 
 */
$token = "user:pitsolu|status:active|is_superuser:true";

$query = new Strukt\Core\TokenQuery($token);

$query->get("user");//pitsolu
$query->get("status");//active
$query->get("is_superuser");//true

$query->has("role");//false
$query->keys();//["user","status","is_superuser"]

$query->token();//original token -- user:pitsolu|status:active|is_superuser:true
$query->set("role","admin");
$token = sprintf("%s|role:admin", $query->token());
$query->reMake();//user:pitsolu|status:active|is_superuser:true|role:admin

/**
 * Complex Token
 */
$token = "contact:1|is:tenant,landlord,prospect";

$query = new Strukt\Core\TokenQuery($token);

$query->get("is");//["tenant","landlord","prospect"];
$query->set("status", ["active","published"]);
$query->reMake();//contact:1|is:tenant,landlord,prospect|status:active,published
```

### Monad

```php
// Linear Equation y = mx + c

$params = array("c"=>12, "m"=>3, "x"=>2)

$y = Monad::create($params)
    ->next(function($m, $x){

        $mx = $m * $x;

        return $mx;
    })
    ->next(function($mx, $c){

        return $mx + $c;
    })
    ->next(function($r){

        return $r;
    });

echo $y->yield();
```

### Messages

```php
use Strukt\Message\Error;
// use Strukt\Message\Info; //Works the same way as Strukt\Messgae\Error

new Error("error 401!");
new Error("error 402!");
new Error("error 404!");

$errors = Error::getMessages();

$message = $errors->last()->yield(); //error 404!
$errors->reset();
$message = $errors->current()->yield(); //error 401!
$errors->next();
$message = $errors->current()->yield(); //error 402!
```

### Json

```php
$l = Strukt\Type\Json::encode(array("fname"=>"Peter", "lname"=>"Pan"));//json string
$m = Strukt\Type\Json::pp($l);//pretty print
$n = Strukt\Type\Json::decode($m);//array
```
