<?php 
/**
 * @author Filippo Finke
 */
namespace Api\Modules;
use Api\SmsApi as SmsApi;

class smssellaitecom implements module {

    private $url = "http://sms.sellaite.com/";

    private $phones;

    public function __construct() {
        SmsApi::log("initialized!");
        $this->fetchPhones();
        SmsApi::log("enabled!");
    }

    private function fetchPhones() {
        $html = file_get_contents($this->url);
        preg_match_all('/phone=([0-9]+?)\'/', $html, $phones);
        $phones = $phones[1];
        foreach($phones as $key => $phone) {
            $phones[$key] = "+".substr($phone, 2);
        }
        $this->phones = $phones;
        SmsApi::log("loaded ".(count($this->phones))." phones!");
    }

    public function getPhones() {
        return $this->phones;
    }

    public function getSms($phone, $limit) {
        SmsApi::log("fetching $limit sms for $phone");
        $phone = str_replace("+", "", $phone);
        $url = $this->url."index.php?phone=00".$phone;
        $html = file_get_contents($url);
        preg_match_all('/<table class=\'sms\'>(.*?)<\/table>/s', $html, $rows);
        $rows = $rows[0];
        $sms = [];
        for($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            preg_match('/from=(.*?)\'/', $row, $from);
            preg_match_all('/SMS message:\'>(.*?)</s', $row, $message);
            preg_match('/time\'>(.*?)</s', $row, $time);
            $message[1][0] = str_replace("\t","",$message[1][0]);
            $sm = array(
                "from" => (isset($from[1]))?urldecode($from[1]):"UNKNOWN",
                "message" => strip_tags((isset($message[1][0]))?$message[1][0]:""),
                "time" => (isset($time[1]))?$time[1]:"UNKNOWN"
            );
            $sms[] = $sm;
        }
        array_splice($sms, $limit);
        return $sms;
    }

}
