<?php namespace E;

use Exception;

defined('_ESPADA') or die(NO_ACCESS);

/**
 * @phpstan-import-type T_UriArgInfo from PageAlias
 * @phpstan-import-type T_UriArgs from PageAlias
 * @phpstan-type T_PageArgs array<string, mixed>
 */

class Page {
    private string $name;
    private string $path;
    /** @var T_PageArgs */
    private array $args;
    /** @var array<string, PageAlias> */
    private array $aliases;

    private string|null $filePath;

    /**
     * @param string $name 
     * @param string $path 
     * @param T_PageArgs $args 
     * @param  array<string, PageAlias> &$aliases 
     * @return void 
     */
    public function __construct(string $name, string $path, array $args, 
            array &$aliases) {
        $this->name = $name;
        $this->path = $path;
        $this->args = $args;

        $this->aliases = &$aliases;
        $this->filePath = null;
    }

    /**
     * @param T_UriArgs $uriArgs 
     * @param string $langName 
     * @return string 
     * @throws Exception 
     */
    public function getAlias(array $uriArgs, string $langName = ''): string {
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
                if (!array_key_exists($aliasPart['name'], $uriArgs["args"])) {
                    print_r($uriArgs);
                    throw new \Exception(
                            "Uri arg `{$aliasPart['name']}` not set.");
                }

                $uri .= $uriArgs["args"][$aliasPart['name']] . '/';
                unset($uriArgs["args"][$aliasPart['name']]);
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

        foreach ($uriArgs['extra'] as $uriPart)
            $uri .= $uriPart . '/';

        unset($uriArgs['extra']);

        foreach ($uriArgs["args"] as $argName => $arg)
            throw new \Exception("Uri arg `{$argName}` does not exist.");

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

            /* $alias["type"] === "ext" */
            $uri .= '*';
        }

        return $uri;
    }

    /**
     * @param string $langName 
     * @return array<T_UriArgInfo> 
     * @throws Exception 
     */
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

    /**
     * @return T_PageArgs
     */
    public function getArgs(): array {
        return $this->args;
    }

    public function getFilePath(): string {
        if ($this->filePath === null) {
            $this->filePath = Package::Path_FromPath($this->path,
                    'pages', '.php');
            if ($this->filePath === null)
                throw new \Exception("Page path `{$this->path}` does not exist.");
        }

        return $this->filePath;
    }

    public function getName(): string {
        return $this->name;
    }

    /**
     * @param T_UriArgs|null $uriArgs 
     * @param string $langName 
     * @param bool $pathOnly 
     * @return string 
     */
    public function getUri(array|null $uriArgs = null, string $langName = '', 
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
