<?php 
/**
 * @author Filippo Finke
 */
namespace Api\Modules;
use Api\SmsApi as SmsApi;

class receivesmsscom implements module {

    private $url = "https://receive-smss.com/";

    private $phones;

    public function __construct() {
        SmsApi::log("initialized!");
        $this->fetchPhones();
        SmsApi::log("enabled!");
    }

    private function fetchPhones() {
        $html = file_get_contents($this->url);
        preg_match_all('/\+[0-9]+/', $html, $phones);
        $this->phones = $phones[0];
        SmsApi::log("loaded ".(count($this->phones))." phones!");
    }

    public function getPhones() {
        return $this->phones;
    }

    public function getSms($phone, $limit) {
        SmsApi::log("fetching $limit sms for $phone");
        $phone = str_replace("+", "", $phone);
        $url = $this->url."sms/$phone/";
        $html = file_get_contents($url);
        preg_match_all('/<tr>(.*?)<\/tr>/s', $html, $rows);
        $rows = $rows[0];
        $sms = [];
        for($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            preg_match_all('/">(.*?)<\/td>/s', $row, $message);
            $sms[] = strip_tags($message[1][1]);
        }
        array_splice($sms, $limit);
        return $sms;
    }

}
