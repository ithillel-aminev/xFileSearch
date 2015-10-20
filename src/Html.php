<?php
/**
 * Created by PhpStorm.
 * User: taminev
 * Date: 20.10.2015
 * Time: 15:22
 */

namespace Taminev\Xfilessearch;


class Html extends Entity{

    public function search($language, $region = false, $time = false)
    {
        $result = array();

        $localeText = strtolower($language);
        if ($region){
            $localeText .= '_' . strtoupper($region);
        }
        $files = glob($this->getSearchDir() . "*$localeText*");

        foreach ($files as $file){
            $result[$localeText][] = realpath($file);
        }

        return $result;
    }
}