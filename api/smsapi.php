<?php
/**
 * @author Filippo Finke
 */
namespace Api;
use Exception;

class SmsApi {

    private static $verbose = false;

    private $activeModules = [];

    public function __construct($verbose = false) {
        self::$verbose = $verbose;
        self::log("loaded");
    }

    public static function log($text) {
        if(self::$verbose) echo date("[H:i:s-d/m/Y]")." ".debug_backtrace()[1]['class']." ".$text."\n";
    }

    public function getSms($phone, $limit = 50) {
        foreach($this->activeModules as $module) {
            SmsApi::log("Searching $phone in module ".get_class($module));
            if(in_array($phone, $module->getPhones())) {
                return $module->getSms($phone, $limit);
            }
        }
        SmsApi::log("$phone not found");
        return false;
    }

    public function getPhones() {
        $phones = array();
        foreach($this->activeModules as $module) {
            $phones = array_merge($phones, $module->getPhones());
        }
        return $phones;
    }

    public function getModules() {
        $modules = glob(__DIR__."/modules/*.php");
        foreach($modules as $key => $module)
        {
            $module = explode("/", $module);
            $module = str_replace(".php", "", end($module));
            $modules[$key] = $module;
            if($module == "module") {
                unset($modules[$key]);
            }
        }
        return $modules;
    }

    public function getActiveModules() {
        return $this->activeModules;
    }

    public function disableModule($module) {
        $module = "Api\\Modules\\$module";
        foreach($this->activeModules as $key => $activeModule) {
            if(get_class($activeModule) == $module) {
                unset($this->activeModules[$key]);
                self::log("Module $module disabled!");
                return true;
            }
        }
        self::log("Module $module is not loaded!");
        return false;
    }

    public function loadModule($module) {
        if(in_array($module, $this->getModules())) {
            $module = "Api\\Modules\\$module";
            $this->activeModules[] = new $module();
            self::log("Module $module loaded!");
            return true;
        } else {
            self::log("Module $module not found!");
            throw new Exception("Module not found!");
        }
    }

}
