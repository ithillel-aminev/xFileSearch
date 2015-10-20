<?php

namespace Taminev\Xfilessearch;


class EntityFactory {
    const PRODUCTS = 0;

    const VIDEOS = 1;

    const HTML = 2;

    const ANIMATION = 3;

    const BONUS_PRODUCTS = 4;

    public static function create($type, $config, $baseUrl)
    {
        switch ($type){
            case EntityFactory::PRODUCTS:
                return new Product($config, $baseUrl);
                break ;
            case EntityFactory::VIDEOS:
                return new Video($config, $baseUrl);
                break ;
            case EntityFactory::HTML:
                return new Html($config, $baseUrl);
                break ;
        }

        return false;
    }
}