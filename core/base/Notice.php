<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Notice
{

    static private $Fields = null;
    static private $Notices = [];

    static public function Add($message)
    {
        $notice = [
            'message' => $message,
            'backtrace' => debug_backtrace(),
            'stack' => [],
        ];

        for ($i = 0; $i < count($notice['backtrace']); $i++) {
            if (array_key_exists('file', $notice['backtrace'][$i])) {
                $notice['stack'][] = $notice['backtrace'][$i]['file'] . ':' .
                        $notice['backtrace'][$i]['line'];
            } else
                $notice['stack'][] = 'Undefined';
        }

        self::$Notices[] = $notice;
    }

    static public function GetAll()
    {
        return self::$Notices;
    }

}
