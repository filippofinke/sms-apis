<?php
/**
 * @author Filippo Finke
 */
use Api\SmsApi;
require __DIR__ .'/../vendor/autoload.php';

$api = new SmsApi(true);
$modules = $api->getModules();
echo "Modules:\n";
foreach($modules as $module) {
    echo "$module\n";
}
$api->loadModule("receivesmsscom");
$phones = $api->getPhones();
echo "Loaded phones: ".count($phones)."\n";

echo "Sms for ".$phones[0]."\n";
$sms = $api->getSms($phones[0], 5);
foreach($sms as $sm) {
    echo $sm."\n";
}
?>