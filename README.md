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
$contactsCol = new \Strukt\Core\Collection("Contacts");
$contactsCol->set("mobile", "+2540770123456");
$contactsCol->set("work-phone", "+2540202345678");

$userCol = new Collection("User");
$userCol->set("contacts", $contactsCol);

$userCol->get("contacts.mobile"); //outputs +2540770123456
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

// $x = Strukt\Builder\CollectionBuilder::getInstance(new \Strukt\Core\Collection())->fromAssoc($s);
// $b = Strukt\Builder\CollectionBuilder(new \Strukt\Core\Collection());
$b = new Strukt\Builder\CollectionBuilder();
$x = $b->fromAssoc($s); //returns \Strukt\Core\Collection
```

## Map

```php
$map = new \Strukt\Core\Map(new \Strukt\Core\Collection());
$map->set("session.user.username", "genewilder");
$map->set("session.user.firstname", "Gene");
$map->set("session.user.surname", "Wilder");
$map->set("db.config.username", "root");
$map->set("db.config.password", "_root");
```

Both `Map` and `Collection` have functions `set` , `get` , `exist` , `remove` The difference between both utilities is that `Map` can `set` and `remove` deep values while `Collection` cannot.

## Events

```php
$credentials = array("username"=>"admin", "password"=>"p@55w0rd");

$login = Strukt\Event\Event::newEvent(function($username, $password) use($credentials){

    return $username == $credentials["username"] && $password == $credentials["password"];
});

$isLoggedIn = $login->getEvent()->apply("admin","p@55w0rd")->exec();
// $isLoggedIn = $login->getEvent()->applyArgs($credentials)->exec();
```
