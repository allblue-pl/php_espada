<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class Page {
    private string $name;
    private string $path;
    private array $args;

    private array $aliases;

    private ?string $filePath;

    public function __construct(string $name, string $path, array $args, array &$aliases) {
        $this->name = $name;
        $this->path = $path;
        $this->args = $args;

        $this->aliases = &$aliases;
        $this->filePath = null;
    }

    public function getArgs(): array {
        return $this->args;
    }

    public function getFilePath(): string {
        if ($this->filePath === null) {
            $this->filePath = Package::Path_FromPath($this->path,
                    'pages', '.php');
            // if ($this->filePath === null)
            //     throw new \Exception("Page path `{$this->path}` does not exist.");
        }

        return $this->filePath;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getAlias(array $args = [], string $langName = ''): string {
        $lang = Langs::Get($langName);
        if ($lang === null)
            throw new \Exception("Language `{$langName}` does not exist.");
        $langName = $lang['name'];

        if (!array_key_exists($langName, $this->aliases))
            throw new \Exception("Page '{$this->name}' doesn't have '{$langName}' alias.");

        $uri = '';

        $aliasParts = $this->aliases[$langName]->getParts();
        foreach ($aliasParts as $aliasPart) {
            if ($aliasPart['type'] === 'text') {
                $uri .= $aliasPart['value'] . '/';
                continue;
            }

            if ($aliasPart['type'] === 'arg') {
                if (!array_key_exists($aliasPart['name'], $args)) {
                    print_r($args);
                    throw new \Exception(
                            "Uri arg `{$aliasPart['name']}` not set.");
                }

                $uri .= $args[$aliasPart['name']] . '/';
                unset($args[$aliasPart['name']]);
                continue;
            }

            // if ($aliasPart['type'] === 'ext') {
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

    public function getAlias_Raw(string $langName = ''): string {
        $lang = Langs::Get($langName);
        if ($lang === null)
            throw new \Exception("Language `{$langName}` does not exist.");
        $langName = $lang['name'];

        if (!array_key_exists($langName, $this->aliases))
            throw new \Exception("Page '{$this->name}' doesn't have '{$langName}' alias.");

        $uri = '';

        $aliasParts = $this->aliases[$langName]->getParts();

        foreach ($aliasParts as $aliasPart) {
            if ($aliasPart['type'] === 'text') {
                $uri .= $aliasPart['value'] . '/';
                continue;
            }

            if ($aliasPart['type'] === 'arg') {
                $uri .= ':' . $aliasPart['name'] . '/';
                continue;
            }

            if ($aliasPart['type'] === 'ext') {
                $uri .= '*';
                continue;
            }
        }

        return $uri;
    }

    public function getAliasArgs(string $langName = ''): array {
        $lang = Langs::Get($langName);
        if ($lang === null)
            throw new \Exception("Language `{$langName}` does not exist.");
        $langName = $lang['name'];

        if (!array_key_exists($langName, $this->aliases))
            throw new \Exception("Page '{$this->name}' doesn't have '{$langName}' alias.");

        $parts = $this->aliases[$langName]->getParts();
        $args = [];
        foreach ($parts as $part) {
            if ($part['type'] !== 'text')
                $args[] = $part;
        }

        return $args;
    }

    public function getUri(?array $uriArgs = null, string $langName = '', 
            bool $pathOnly = true): string {
        return Uri::Page($this->name, $uriArgs, $langName, $pathOnly);
    }

    public function getUri_Raw(string $langName = '', bool $pathOnly = true): string {
        return Uri::Page_Raw($this->name, $langName, $pathOnly);
    }

    public function hasAlias(string $langName): bool {   
        return array_key_exists($langName, $this->aliases);
    }
}
