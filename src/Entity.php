<?php
/**
 * Created by PhpStorm.
 * User: taminev
 * Date: 20.10.2015
 * Time: 14:31
 */

class Entity {

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
        if (!$this->getSearchDir()){
            return null;
        }
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
                $files[$text] = $this->rglob($this->getSearchDir() . "*$text*");
            }
        } else {
            $files = $this->rglob($this->getSearchDir() . "*");
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
        if (!isset($this->config['subDir']) || empty($this->config['subDir'])){
            return false;
        }
        return Parser::normalizeDirectory(Parser::normalizeDirectory($this->base_url) . $this->config['subDir']);
    }

    protected function filterByText($array, $text)
    {
        return array_filter($array, function($val) use ($text){return strpos($val, $text) !== false;});
    }

    protected function filterByTime($array, $time)
    {
        return array_filter($array, function($file) use ($time) {return filemtime($file) > $time;});
    }

    private function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->rglob($dir.DIRECTORY_SEPARATOR.basename($pattern), $flags));
        }
        return $files;
    }



}