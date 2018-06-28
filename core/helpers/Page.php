<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Page
{

    private $name = '';
    private $path = '';
    private $args = null;

    private $aliases = null;

    private $filePath = null;

    public function __construct($name, $path, $args, &$aliases)
    {
        $this->name = $name;
        $this->path = $path;
        $this->args = $args;

        $this->aliases = &$aliases;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function getFilePath()
    {
        if ($this->filePath === null) {
            $this->filePath = Package::Path_FromPath($this->path,
                    'pages', '.php');
            if ($this->filePath === null)
                throw new \Exception("Page path `{$this->path}` does not exist.");
        }

        return $this->filePath;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAlias($args = [], $langName = '')
    {
        $lang = Langs::Get($langName);
        if ($lang === null)
            throw new \Exception("Language `{$langName}` does not exist.");
        $langName = $lang['name'];

        if (!array_key_exists($langName, $this->aliases))
            throw new \Exception("Page doesn't have `{$langName}` alias.");

        $uri = '';

        $alias_args = $this->aliases[$langName]->getArgs();
        foreach ($alias_args as $alias_arg) {
            if ($alias_arg['type'] === 'text') {
                $uri .= $alias_arg['value'] . '/';
                continue;
            }

            if ($alias_arg['type'] === 'arg') {
                if (!array_key_exists($alias_arg['name'], $args)) {
                    print_r($args);
                    throw new \Exception(
                            "Uri arg `{$alias_arg['name']}` not set.");
                }

                $uri .= $args[$alias_arg['name']] . '/';
                unset($args[$alias_arg['name']]);
                continue;
            }

            // if ($alias_arg['type'] === 'ext') {
            //     if (array_key_exists('_extra', $args)) {
            //         foreach ($args['_extra'] as $uri_part)
            //             $uri .= $uri_part . '/';
            //
            //         unset($args['_extra']);
            //     }
            // }
        }

        if (array_key_exists('_extra', $args)) {
            foreach ($args['_extra'] as $uri_part)
                $uri .= $uri_part . '/';

            unset($args['_extra']);
        }

        foreach ($args as $arg_name => $arg)
            throw new \Exception("Uri arg `{$arg_name}` does not exist.");

        return $uri;
    }

    public function getAliasArgs($langName = '')
    {
        $lang = Langs::Get($langName);
        if ($lang === null)
            throw new \Exception("Language `{$langName}` does not exist.");
        $langName = $lang['name'];

        if (!array_key_exists($langName, $this->aliases))
            throw new \Exception("Page doesn't have `{$langName}` alias.");

        return $this->aliases[$langName]->getArgs();
    }

    public function getUri($uriArgs = null, $langName = '', $pathOnly = true)
    {
        return Uri::Page($this->name, $uriArgs, $langName, $pathOnly);
    }

}
