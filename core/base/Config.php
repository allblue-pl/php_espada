<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Config
{

    static public function IsType($type)
    {
        $types = explode(',', str_replace(' ', '', trim(ECONFIG_TYPE)));

        return in_array($type, $types);
    }

}
