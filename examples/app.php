<?php
/**
 * @author Filippo Finke
 */
use Api\SmsApi;
require __DIR__ .'/../vendor/autoload.php';

function showActiveModules() {
    global $api;
    echo "ActiveModules\n";
    $activeModules = $api->getActiveModules();
    if(count($activeModules) > 0) {
        echo "Index\tModule\n";
        foreach($activeModules as $key => $module) {
            echo $key."\t".get_class($module)."\n";
        }
    } else {
        echo "No active modules\n";
    }
}

function showModules() {
    global $api;
    $modules = $api->getModules();
    if(count($modules) > 0) {
        echo "Index\tModule\n";
        foreach($modules as $key => $module) {
            echo $key."\t".$module."\n";
        }
    } else {
        echo "No modules installed\n";
    }
}

function moduleManager($enable) {
    global $api;
    $modules = $api->getModules();
    while(true) {
        $module = readline("Select a module to ".(($enable)?"enable":"disable")." (Enter to exit): ");
        if($module == "") break;
        if(isset($modules[$module])) {  
            if($enable) $api->enableModule($modules[$module]); 
            else $api->disableModule($modules[$module]); 
        } else  
        { 
            echo "Error, module not found!\n"; 
        }
    }
}

function showPhones() {
    global $api;
    $phones = $api->getPhones();
    echo "Phones\n";
    if(count($phones) > 0) {
        echo "Index\tPhone\n";
        foreach($phones as $key => $phone) {
            echo $key."\t$phone\n";
        }
    } else {
        echo "No phones loaded\n";
    }
}

function getSms() {
    global $api;
    $phone = readline("Insert the phone number: ");
    $limit = readline("Insert a limit (default 50): ");
    if(!is_numeric($limit)) $limit = 50;
    $sms = $api->getSms($phone, $limit);
    echo "Sms\n";
    if(is_array($sms) && count($sms) > 0) {
        echo "Index\tMessage\n";
        foreach($sms as $key => $sm) {
            echo $key."\t$sm\n";
        }
    } else {
        echo "No sms loaded\n";
    }
}

$api = new SmsApi(true);
system('clear');
while(true) {
    echo "\n\nSmsApi by @filippofinke\n\n";
    echo "0 - See enabled modules\n";
    echo "1 - Enable a module\n";
    echo "2 - Disable a module\n";
    echo "3 - Get all phones\n";
    echo "4 - Get sms of a specific phone\n";
    $command = readline("Select a command: ");
    system('clear');
    if(is_numeric($command)) {
        if($command == 0) {
            showActiveModules();
        } else if($command == 1) {
            showModules();
            moduleManager(true);
        } else if($command == 2) {
            showModules();
            moduleManager(false);
        } else if($command == 3) {
            showPhones();
        } else if($command == 4) {
            getSms();
        }
    }
}
?>