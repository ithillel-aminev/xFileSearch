<?php

namespace Taminev\Xfilessearch;


class Parser {

    private $parseConfig;

    private $generalConfig;

    public function __construct()
    {
        $this->generalConfig = parse_ini_file(dirname(__DIR__).'../config/general.ini');
        $this->parseConfig = parse_ini_file(dirname(__DIR__).'../config/parse.ini', true);
    }

    public function parse(){
        $result = array();
        $type = false;
        $language = $this->getValue('language');
        $region = $this->getValue('region');
        $time = $this->getValue('time');
        $languageCode = $language ? strtolower($language) : '';
        $regionCode = $region ? strtoupper($region) : '';
        $locale = $languageCode;
        if ($regionCode)
            $locale .= "_". $regionCode;

        if(!$languageCode) {
            throw new \Exception('no language');
        }
        foreach ($this->parseConfig as $key=>$config){
            switch($key){
                case 'products':
                    $type = EntityFactory::PRODUCTS;
                    break;
                case 'videos':
                    $type = EntityFactory::VIDEOS;
                    break;
                case 'html':
                    $type = EntityFactory::HTML;
                    break;

            }
            if ($type !== false){
                $obj = EntityFactory::create($type, $config, $this->generalConfig['base_url']);
                $result[$key][$locale] = $obj->search($languageCode, $regionCode, $time);
            }

        }

        return json_encode($result);

    }

    private function getValue($key)
    {
        if (empty($key) || !is_string($key) || !isset($_GET[$key])){
            return false;
        }

        $ret = $_GET[$key];

        if (is_string($ret))
            return stripslashes(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret))));

        return $ret;
    }

    public static function normalizeDirectory($directory)
    {
        $last = $directory[strlen($directory) - 1];

        if (in_array($last, array('/', '\\')))
        {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;
            return $directory;
        }

        $directory .= DIRECTORY_SEPARATOR;
        return $directory;
    }

    public function getFullDirNameForEntity($entityName){
        if(!isset($this->parseConfig[$entityName])){
            return false;
        }
        $subDir = isset($this->parseConfig[$entityName]['subDir']) ? $this->parseConfig[$entityName]['subDir'] : '';
        return Parser::normalizeDirectory($this->generalConfig['base_url']) . $subDir;
    }



}