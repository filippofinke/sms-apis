<?php 
/**
 * @author Filippo Finke
 */
namespace Api\Modules;
use Api\SmsApi as SmsApi;

class smstibocom implements module {

    private $url = "https://smstibo.com/";

    private $phones;

    public function __construct() {
        SmsApi::log("initialized!");
        $this->fetchPhones();
        SmsApi::log("enabled!");
    }

    private function request($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $headers = array();
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0';
        $headers[] = 'Accept: text/css,*/*;q=0.1';
        $headers[] = 'Accept-Language: it-IT,it;q=0.8,en-US;q=0.5,en;q=0.3';
        $headers[] = 'Connection: keep-alive';
        $headers[] = 'Te: Trailers';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    private function fetchPhones() {
        $this->phones = array(
            array(),
            array()
        );
        $html = $this->request($this->url);
        preg_match_all('/href="\/(.*?)">Show me numbers/', $html, $urls);
        $urls = $urls[1];
        SmsApi::log("found ".count($urls)." sections!");
        foreach($urls as $url) {
            SmsApi::log("fetching phones from $url");
            $content = $this->request($this->url.$url);
            preg_match_all('/href="\/(.*?)">Check Number/', $content, $phones_url);
            $phones_url = $phones_url[1];
            if(count($phones_url) > 0)
            {
                foreach($phones_url as $phone_url) {
                    $info = explode("-", $phone_url);
                    $phone = "+".end($info);
                    $this->phones[0][] = $phone;
                    $this->phones[1][] = $phone_url;
                }
            }
            else
            {
                SmsApi::log("no free phones in this section!");
            }
        }
        SmsApi::log("loaded ".(count($this->phones[0]))." phones!");
    }

    public function getPhones() {
        return $this->phones[0];
    }

    public function getSms($phone, $limit) {
        SmsApi::log("fetching $limit sms for $phone");
        $phone = str_replace("+", "", $phone);
        $key = array_search($phone, $this->phones[0]);
        $url = $this->url.$this->phones[1][$key];
        $html = $this->request($url);
        preg_match_all('/<div style="margin-bottom:20px;word-wrap: break-word;">(\s*.*?)(\s*)<\/div>(\s*)<\/div>(\s*)/s', $html, $rows);
        $rows = $rows[0];
        $sms = [];
        for($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            preg_match('/r">(.*?)<\//s', $row, $time);
            $message = preg_grep('/<div>(.*)<\/div>/', explode("\n", $row));
            preg_match('/">(.*?)<\//', $message[3], $from);
            $sm = array(
                "from" => (isset($from[1]))?$from[1]:"UNKNOWN",
                "message" => trim(strip_tags((isset($message[4]))?$message[4]:"")),
                "time" => (isset($time[1]))?$time[1]:"UNKNOWN"
            );
            $sms[] = $sm;
        }
        array_splice($sms, $limit);
        return $sms;
    }

}
