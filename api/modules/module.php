<?php
/**
 * @author Filippo Finke
 */
namespace Api\Modules;

interface module {

    public function __construct();
    public function getPhones();
    public function getSms($phone, $limit);

}