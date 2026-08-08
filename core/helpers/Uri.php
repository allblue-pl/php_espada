<?php namespace E;

use Exception;

defined('_ESPADA') or die(NO_ACCESS);

/**
 * @phpstan-import-type T_UriArgs from PageAlias
 * @package E
 */

class Uri {
	static private Uri|null $Instance = null;

	static public function Base(bool $pathOnly = true): string {
        assert(self::$Instance !== null);

		if ($pathOnly)
			return self::$Instance->base;

		return Uri::Domain() . self::$Instance->base;
	}

	static public function Current(bool $pathOnly = true): string {
		if (self::$Instance === null) {
			throw new \Exception('Cannot get current uri' .
					' before initialization.');
		}

		if ($pathOnly)
			return self::$Instance->uri;

		return Uri::Domain() . self::$Instance->uri;
	}

	static public function Domain(): string {
        /** @phpstan-ignore notIdentical.alwaysTrue */
		if (SITE_DOMAIN !== '')
			return SITE_DOMAIN;

        /** @phpstan-ignore deadCode.unreachable */
		return $_SERVER['HTTP_HOST'];
	}

	static public function File(string $path, bool $pathOnly = true): string {
		$fileUri = Package::Uri_FromPath($path, 'front', '');
		if ($fileUri === null) {
			Notice::Add("Cannot find front file: {$path}.");
            $fileUri = "#";
        }

		if ($pathOnly)
			return $fileUri;

		return self::Domain() . $fileUri;
	}

	static public function Media(string $packageName, string $filePath): string|null {
		$packageName = mb_strtolower($packageName);
		$fs_file_path = PATH_MEDIA . '/' . $packageName . '/' . $filePath;

		if (!file_exists($fs_file_path))
			return null;

		return URI_MEDIA . $packageName . '/' . $filePath;
	}

    /**
     * 
     * @param string|null $pageName 
     * @param T_UriArgs|null $uriArgs 
     * @param string $langName 
     * @param bool $pathOnly 
     * @param bool $includeBase 
     * @return string 
     * @throws Exception 
     */
	static public function Page(string|null $pageName = null, array|null $uriArgs = null,
			string $langName = '', bool $pathOnly = true, bool $includeBase = true): string {
		if ($pageName === null) {
			$pageName = Pages::GetName();

			if ($uriArgs === null)
				$uriArgs = Args::Uri_All();
		}

		if ($uriArgs === null) {
			$uriArgs = [
                "args" => [],
                "extra" => [],
            ];
        }

		$page = Pages::Get($pageName);

		if ($page === null)
			throw new \Exception("Page `{$pageName}` does not exist.");

		$lang = Langs::Get($langName);
		if ($lang === null)
			throw new \Exception("Lang `{$langName}` does not exist.");

		$langName = $lang['name'];

		$pageUri = $page->getAlias($uriArgs, $langName);

        $uri = '';
        if ($includeBase)
            $uri .= Uri::Base($pathOnly);
        if ($lang['alias'] !== '')
            $uri .= $lang['alias'] . '/';

		return $uri . $pageUri;
    }
    
    static public function Page_Raw(string|null $pageName = null, string $langName = '', 
            bool $pathOnly = true, bool $includeBase = true): string {
        if ($pageName === null)
			$pageName = Pages::GetName();

		$page = Pages::Get($pageName);

		if ($page === null)
			throw new \Exception("Page `{$pageName}` does not exist.");

		$lang = Langs::Get($langName);
		if ($lang === null)
			throw new \Exception("Lang `{$langName}` does not exist.");

        $langName = $lang['name'];

		$pageUri = $page->getAlias_Raw($langName);

        $uri = '';
        if ($includeBase)
            $uri .= Uri::Base($pathOnly);
        if ($lang['alias'] !== '')
            $uri .= $lang['alias'] . '/';

		return $uri . $pageUri;
    }

    /**
     * @param list<string> $pageNames 
     * @return list<string> 
     */
	static public function Pages(array $pageNames): array {
		$uris = [];

		foreach ($pageNames as $pageName)
			$uris[] = self::Page($pageName);

		return $uris;
	}

    static public function Protocol(): string {
        if (mb_strpos(self::Domain(), 'https://'))
                return 'https://';

        return 'http://';
    }

	static public function Site(bool $pathOnly = true): string {
        assert(self::$Instance !== null);

		if ($pathOnly)
			return self::$Instance->uri;

		return self::Domain() . self::$Instance->uri;
    }
    
    /**
     * @param array<string, string> $getArgs 
     * @return string 
     */
    static public function Query(array $getArgs): string {
        $query = '';

        $first = true;
        foreach ($getArgs as $argName => $argValue) {
            $query .= ($first ? '?' : '&') . $argName . '=' . urlencode($argValue);
            $first = false;
        }

        return $query;
    }


	private string $base;
    /** @var list<string> */
	private array $args;
	private string $uri;

	public function __construct(string $uri_Raw) {
		if (self::$Instance !== null)
			throw new \Exception("Uri already created.");

		self::$Instance = $this;

		/* Base */
        $uri = urldecode($uri_Raw);
        $this->base = SITE_BASE; // dirname($_SERVER['PHP_SELF']);

        /** @phpstan-ignore identical.alwaysFalse, identical.alwaysFalse, booleanOr.alwaysFalse */
		if ($this->base === '' || $this->base === '\\')
			$this->base = '/';
        /** @phpstan-ignore notIdentical.alwaysFalse */
		else if ($this->base[mb_strlen($this->base) - 1] !== '/')
			$this->base = $this->base . '/';

		/* Uri Args */
		$uri = substr($uri, mb_strlen($this->base));
        $uri_Arr = explode('?', $uri);
        $query = count($uri_Arr) > 1 ? 
                mb_substr($uri, mb_strlen($uri_Arr[0])) : '';

        $uri = $uri_Arr[0];
        $this->args = explode('/', $uri);
		if ($this->args[count($this->args) - 1] === '')
			array_pop($this->args);
        for ($i = 0; $i < count($this->args); $i++)
            $this->args[$i] = $this->parseArg($this->args[$i]);

        $this->uri = $this->base . implode('/', $this->args) . $query;
	}

	public function getArg(int $index): string|null {
		if (isset($this->args[$index]))
			return $this->args[$index];

		return null;
	}

    /**
     * @return list<string> 
     */
	public function getArgs(): array {
		return $this->args;
	}

	public function getArgs_Length(): int {
		return count($this->args);
	}

    public function getUri(): string {
        return $this->uri;
    }


    private function parseArg(string $arg_Raw): string {
        $arg = "";
        $allowedChars = 'qwertyuiopasdfghjklzxcvbnm' . 
                'QWERTYUIOPASDFGHJKLZXCVBNM' . 
                '0123456789' .
                '-_';
        for ($i = 0; $i < mb_strlen($arg_Raw); $i++) {
            if (mb_strpos($allowedChars, $arg_Raw[$i]) > -1)
                $arg .= (string)$arg_Raw[$i];
        }

        return $arg;
    }
}
