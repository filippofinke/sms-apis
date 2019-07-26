<?php
use Api\SmsApi;
require __DIR__ .'/../vendor/autoload.php';

$api = new SmsApi(true);
$api->enableModule("smssellaitecom");
$phones = $api->getPhones();
echo json_encode($phones);

$phone = $phones[0];
echo "\nSms for $phone\n";
$sms = $api->getSms($phone, 5);
foreach($sms as $sm) {
    echo json_encode($sm)."\n";
}
?>