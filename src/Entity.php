<?php
/**
 * Created by PhpStorm.
 * User: taminev
 * Date: 20.10.2015
 * Time: 14:31
 */

namespace Taminev\Xfilessearch;


abstract class Entity {

    protected $name;

    protected $base_url;

    protected $config;

    public function __construct($config, $base_url)
    {
        $this->base_url = $base_url;
        $this->config = $config;
    }

    public function search($language, $region = false, $time = false)
    {
        $languageCode = strtolower($language);
        $regionCode = ($region) ? strtoupper($region) : '';

        // text filter
        $texts = array();
        $files = array();
        if (isset($this->config['fileNameContains'])){
            $texts = $this->config['fileNameContains'];
        }
        if ($texts){
            foreach ($texts as $text){
                $files[$text] = glob($this->getSearchDir() . "*$text*");
            }
        } else {
            $files = glob($this->getSearchDir() . "*");
        }

        // locale filter
        $is_numeric = array_key_exists(0, $files);
        if ($is_numeric) {
            $files = $this->filterByText($files, $languageCode);
            if ($regionCode)
                $files = $this->filterByText($files, $regionCode);
        } else {
            foreach ($files as $key=>$fileGroup) {
                $files[$key] = $this->filterByText($fileGroup, $languageCode);
                if ($regionCode)
                    $files[$key] = $this->filterByText($fileGroup, $regionCode);
            }

        }

        // time filter
        if ($time) {
            if ($is_numeric) {
                $files = $this->filterByTime($files, $time);
            } else {
                foreach ($files as $key=>$fileGroup) {
                    $files[$key] = $this->filterByTime($fileGroup, $time);
                }

            }
        }

        return $files;

    }

    public function getSearchDir(){
        if(!isset($this->config)){
            return false;
        }
        $subDir = isset($this->config['subDir']) ? $this->config['subDir'] : '';
        return Parser::normalizeDirectory(Parser::normalizeDirectory($this->base_url) . $subDir);
    }

    protected function filterByText($array, $text)
    {
        return array_filter($array, function($val) use ($text){return strpos($val, $text) !== false;});
    }

    protected function filterByTime($array, $time)
    {
        return array_filter($array, function($file) use ($time) {return filemtime($file) > $time;});
    }


}