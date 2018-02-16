<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class PageAlias
{

    private $args = [];

    public function __construct($uri)
    {
        $this->parseUri($uri);
    }

    public function checkUriArgs($args)
    {
        $args_length = count($this->args);

        $extra_args = false;
        if ($args_length > 0) {
            $last_arg = $this->args[$args_length - 1];
            if ($last_arg['type'] === 'ext') {
                $extra_args = true;
                $args_length--;
            }
        }

        if (!$extra_args) {
            if ($args_length !== count($args))
                return null;
        } else {
            if ($args_length > count($args))
                return null;
        }

        $uri_args = [];
        for ($i = 0; $i < $args_length; $i++) {
            $arg = $this->args[$i];

            if ($arg['type'] === 'text') {
                if ($arg['value'] !== $args[$i])
                    return null;

                continue;
            }

            if ($arg['type'] === 'arg') {
                $uri_args[$arg['name']] = $args[$i];

                continue;
            }

            if ($arg['type'] === 'ext')
                continue;
        }

        $uri_args['_extra'] = array_splice($args, $args_length);

        return $uri_args;
    }

    public function getArgs()
    {
        return $this->args;
    }

    private function parseUri($uri)
    {
        $uri_array = explode('/', $uri);

        $this->args = [];
        if (count($uri_array) === 1)
            if ($uri_array[0] === '')
                return;

        foreach ($uri_array as $uri_part) {
            if ($uri_part[0] === ':') {
                $this->args[] = [
                    'type' => 'arg',
                    'name' => substr($uri_part, 1)
                ];
            } else if ($uri_part === '*') {
                $this->args[] = [
                    'type' => 'ext'
                ];
            } else {
                $this->args[] = [
                    'type' => 'text',
                    'value' => $uri_part
                ];
            }
        }
    }

}
