# sms-apis
-----

## Introduction

Simple library that allows the use of free-sms-receive sites through php. This library is modular, so it contains several websites of this type.

## Getting started
Clone the repository

```git clone https://github.com/filippofinke/sms-apis```

Run composer

```composer dumpautoload -o```

Use the library 

```
<?php
use Api\SmsApi;
require __DIR__ .'/vendor/autoload.php';

$api = new SmsApi(true);
// See the documentation for methods.
?>
```

## Create a module

Being this library modular you can create a specific module for any website of this kind. To create a module you need to create a class that implements the interface ```Api\Modules\module```. After you have created the module and implemented all the methods of the interface you just have to move it into the modules folder.

Methods you must implement:
```
public function __construct();
public function getPhones();
public function getSms($phone, $limit);
```

Example

```
<?php 
namespace Api\Modules;
use Api\SmsApi as SmsApi;

class examplemodule implements module {

    private $url = "urlofthewebsite";

    private $phones;

    public function __construct() {
        SmsApi::log("initialized!");
        $this->fetchPhones();
        SmsApi::log("enabled!");
    }

    private function fetchPhones() {
        //logic to fetch phones
    }

    public function getPhones() {
        return $this->phones;
    }

    public function getSms($phone, $limit) {
        //logic to get sms
    }
}

```


## Status
I am currently working on these modules:

- https://receive-smss.com/ Almost done
- https://sms-receive.net/
- http://sms.sellaite.com/
- https://smstibo.com/
- http://7sim.net/
- https://sms-online.co/
- http://receivesms.cc/
- https://miracletele.com/
- https://receive-sms.cc/