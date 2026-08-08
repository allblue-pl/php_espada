<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

/**
 * @phpstan-type T_UriArgInfo array{
 *   name: string,
 *   type: "arg"|"ext"|"text",
 *   value: string,
 * }|list<string>
 * @phpstan-type T_UriArgs array{
 *   extra: list<string>,
 *   args: array<string, string>,
 * }
 */

class PageAlias {
    /** @var array<T_UriArgInfo> */
    private array $args;

    public function __construct(string $uri) {
        $this->args = [];
        $this->parseUri($uri);
    }

    /**
     * 
     * @param list<string> $args 
     * @return null|T_UriArgs
     */
    public function checkUriArgs(array $args): array|null {
        $args_Length = count($this->args);

        $extraArgs = false;
        if ($args_Length > 0) {
            $lastArg = $this->args[$args_Length - 1];
            if ($lastArg['type'] === 'ext') {
                $extraArgs = true;
                $args_Length--;
            }
        }

        if (!$extraArgs) {
            if ($args_Length !== count($args))
                return null;
        } else {
            if ($args_Length > count($args))
                return null;
        }

        $uriArgs = [
            "extra" => null,
            "args" => [],
        ];
        for ($i = 0; $i < $args_Length; $i++) {
            $arg = $this->args[$i];

            if ($arg['type'] === 'text') {
                if ($arg['value'] !== $args[$i])
                    return null;

                continue;
            }

            if ($arg['type'] === 'arg') {
                $uriArgs["args"][$arg['name']] = $args[$i];

                continue;
            }

            // if ($arg['type'] === 'ext')
            //     continue;
        }
        $uriArgs["extra"] = array_splice($args, $args_Length);

        return $uriArgs;
    }

    /**
     * @return array<T_UriArgInfo>
     */
    public function getParts(): array {
        return $this->args;
    }

    private function parseUri(string $uri): void {
        $uriArray = explode('/', $uri);

        $this->args = [];
        if (count($uriArray) === 1)
            if ($uriArray[0] === '')
                return;

        foreach ($uriArray as $uri_part) {
            if ($uri_part[0] === ':') {
                $this->args[] = [
                    'type' => 'arg',
                    'name' => substr($uri_part, 1),
                    'value' => "",
                ];
            } else if ($uri_part === '*') {
                $this->args[] = [
                    'type' => 'ext',
                    'name' => "",
                    'value' => "",
                ];
            } else {
                $this->args[] = [
                    'type' => 'text',
                    'name' => "",
                    'value' => $uri_part,
                ];
            }
        }
    }

}
