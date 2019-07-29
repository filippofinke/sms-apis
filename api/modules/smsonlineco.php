<?php 
/**
 * @author Filippo Finke
 */
namespace Api\Modules;
use Api\SmsApi as SmsApi;

class smsonlineco implements module {

    private $url = "https://sms-online.co/receive-free-sms";

    private $phones;

    public function __construct() {
        SmsApi::log("initialized!");
        $this->fetchPhones();
        SmsApi::log("enabled!");
    }

    private function fetchPhones() {
        $this->phones = array();
        $html = file_get_contents($this->url);
        preg_match_all('/number">(.*?)<\//', $html, $phones);
        $phones = $phones[1];
        foreach($phones as $phone) {
            $this->phones[] = "+".preg_replace("/[^0-9]/", "", $phone );
        }
        SmsApi::log("loaded ".(count($this->phones))." phones!");
    }

    public function getPhones() {
        return $this->phones;
    }

    public function getSms($phone, $limit) {
        SmsApi::log("fetching $limit sms for $phone");
        $phone = str_replace("+", "", $phone);
        $url = $this->url.'/'.$phone;
        $html = file_get_contents($url);
        preg_match_all('/class="list-item">(.*?)<\/div> <\/div>/s', $html, $rows);
        $rows = $rows[1];
        $sms = [];
        for($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            if(strpos($row, "Get 2 UBER rides for free") !== false) continue;
            preg_match('/title">(.*?)<\/h3>/', $row, $from);
            preg_match('/<span>(.*?)<\/span>/', $row, $time);
            $message = explode('break-word">', $row)[1];
            $from = trim($from[1]);
            $sm = array(
                "from" => (isset($from))?$from:"UNKNOWN",
                "message" => trim(strip_tags((isset($message))?$message:"")),
                "time" => (isset($time[1]))?$time[1]:"UNKNOWN"
            );
            $sms[] = $sm;
        }
        array_splice($sms, $limit);
        return $sms;
    }

}
