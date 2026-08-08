<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

/**
 * @phpstan-type T_Notice array{
 *   message: string,
 *   backtrace: mixed,
 *   stack: array<string>,
 * }
 * @package E
 */

class Notice {
    // static private $Fields = null;
    /** @var list<T_Notice> */
    static private array $Notices = [];

    static public function Add(string $message): void {
        $notice = [
            'message' => $message,
            'backtrace' => debug_backtrace(),
            'stack' => [],
        ];

        for ($i = 0; $i < count($notice['backtrace']); $i++) {
            if (array_key_exists('file', $notice['backtrace'][$i])) {
                if (!array_key_exists("line", $notice['backtrace'][$i]))
                    continue;

                $notice['stack'][] = $notice['backtrace'][$i]['file'] . ':' .
                        $notice['backtrace'][$i]['line'];
            } else
                $notice['stack'][] = 'Undefined';
        }

        self::$Notices[] = $notice;
    }

    /**
     * @return list<T_Notice> 
     */
    static public function GetAll(): array {
        return self::$Notices;
    }
}
