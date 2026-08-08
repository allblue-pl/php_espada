<?php namespace E;

use Exception;

defined('_ESPADA') or die(NO_ACCESS);

/**
 * @package E
 * @phpstan-import-type T_PageArgs from Page
 */

class Pages {
	static private Pages|null $Instance = null;

	static public function Get(string $pageName = '', string $langName = ''): Page|null {
        assert(self::$Instance !== null);

        $lang = Langs::Get($langName);
        if ($lang === null)
            throw new \Exception("Lang '{$langName}' does not exist.");

		$langName = $lang['name'];

		if ($pageName === '') {
            if (self::$Instance->currentPageName === null)
                throw new \Exception("Page not set.");

			$pageName = self::$Instance->currentPageName;
        }

		if (!isset(self::$Instance->pages[$pageName]))
			return null;

		return self::$Instance->pages[$pageName];
    }
    
    /**
     * @param string $langName 
     * @return list<Page> 
     */
    static public function GetAll(string $langName = ''): array {
        assert(self::$Instance !== null);

        $lang = Langs::Get($langName);
        if ($lang === null)
            throw new \Exception("Lang '{$langName}' does not exist.");

        $langName = $lang['name'];

        $pages = [];
        foreach (self::$Instance->pages as $page) {
            if (!$page->hasAlias($langName))
                continue;
            $pages[] = $page;
        }

        return $pages;
    }

	static public function GetName(): string {
        $page = self::Get('');
        if ($page === null)
            throw new \Exception("No default page set.");

		return $page->getName();
	}


	private Langs $langs;
    /** @var array<string, Page> */
	private array $pages;
    /** @var array<string, array<string, PageAlias>> */
	private array $pagesAliases;

	private string|null $currentPageName;

    /** @var array<string, string> */
	private array $errorPageNames;
    /** @var array<string, string> */
	private array $notFoundPageNames;


	public function __construct(Langs $langs, Uri $uri) {
		if (self::$Instance !== null)
			throw new \Exception('Pages already created.');

		self::$Instance = $this;

		$this->langs = $langs;
        $this->pages = [];
        $this->pagesAliases = [];

        $this->currentPageName = null;

        $this->errorPageNames = [];
        $this->notFoundPageNames = [];

		$site_path = PATH_ESITE . '/site.php';
		if (!File::Exists($site_path)) {
				throw new \Exception('Pages file `' . $site_path .
				'` does not exist.');
		}

		$this->requireSitePath($site_path);

		$args_offset = $langs->parseUri($uri);

		$this->parseUri($uri, $args_offset);
	}

    /**
     * @param string $name 
     * @param string $path 
     * @param T_PageArgs $args 
     * @return SitePage 
     * @throws Exception 
     */
	public function addPage(string $name, string $path, array $args): SitePage {
		if (isset($this->pages[$name]))
			throw new \Exception("Page `{$name}` already exists.");

        if ($name === '')
            throw new \Exception("Page name cannot be empty.");

		$this->pagesAliases[$name] = [];
		$this->pages[$name] = new Page($name, $path, $args, 
                $this->pagesAliases[$name]);

		return new SitePage($this, $name);
	}

	public function addPageAlias(string $langName, string $pageName, string $uri): void {
		$lang = $this->langs->getLang($langName);
		if ($lang === null)
			throw new \Exception("Language `{$langName}` does not exist.");
		$langName = $lang['name'];

		$page_alias = new PageAlias($uri);

        $this->pagesAliases[$pageName][$lang['name']] = $page_alias;
	}

    public function getErrorPageName(string $langName): string|null {
        if (!array_key_exists($langName, $this->errorPageNames))
            return null;

        return $this->errorPageNames[$langName];
    }

	private function parseUri(Uri $uri, int $argsOffset): void {
		$lang = Langs::Get();
        if ($lang === null)
            throw new \Exception("Default lang not set.");

		$langName = $lang['name'];

		$args = $uri->getArgs();
		$args = array_splice($args, $argsOffset);

		foreach ($this->pagesAliases as $pageName => $pageAliases) {
			if (!isset($pageAliases[$langName]))
				continue;

			$alias = $pageAliases[$langName];

			$uriArgs = $alias->checkUriArgs($args);
			if ($uriArgs === null)
				continue;

			$page = $this->pages[$pageName];
			new Args($page->getArgs(), $uriArgs);

			$this->currentPageName = $pageName;

			return;
		}

		header('HTTP/1.0 404 Not Found');

		if (isset($this->notFoundPageNames[$langName])) {
			$this->currentPageName = $this->notFoundPageNames[$langName];
			return;
		}

		throw new \Exception('Page not found.');
	}

	public function setErrorPage(string $pageName, string $langName): void {
		if (!isset($this->pages[$pageName]))
			throw new \Exception("Page `{$pageName}` does not exist.");

		$lang = $this->langs->getLang($langName);
        if ($lang === null)
            throw new \Exception("Cannopt get lang '{langName}'.");

		$this->errorPageNames[$lang['name']] = $pageName;
	}

	public function setNotFoundPage(string $pageName, string $langName): void {
		if (!isset($this->pages[$pageName]))
			throw new \Exception("Page `{$pageName}` does not exist.");

		$lang = $this->langs->getLang($langName);
        if ($lang === null)
            throw new \Exception("Cannopt get lang '{$langName}'.");

		$this->notFoundPageNames[$lang['name']] = $pageName;
	}

	private function requireSitePath(string $sitePath): void {
		$eSite = new SitePages($this->langs, $this);

		require($sitePath);
	}

}
