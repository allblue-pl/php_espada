<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Pages {

	static private $Instance = null;

	static public function Get($pageName = '', $langName = '')
	{
		$langName = Langs::Get($langName)['name'];

		if ($pageName === '')
			$pageName = self::$Instance->currentPageName;

		if (!isset(self::$Instance->pages[$pageName]))
			return null;

		return self::$Instance->pages[$pageName];
    }
    
    static public function GetAll($langName = '') {
        $langName = Langs::Get($langName)['name'];

        $pages = [];
        foreach (self::$Instance->pages as $page) {
            if (!$page->hasAlias($langName))
                continue;
            $pages[] = $page;
        }

        return $pages;
    }

	static public function GetName()
	{
		return self::Get('')->getName();
	}


	private $langs = null;
	private $pages = [];
	private $pagesAliases = [];

	private $currentPageName = null;

	private $errorPageNames = [];
	private $notFoundPageNames = [];


	public function __construct(Langs $langs, Uri $uri)
	{
		if (self::$Instance !== null)
			throw new \Exception('Pages already created.');

		self::$Instance = $this;

		$this->langs = $langs;

		$site_path = PATH_ESITE . '/site.php';
		if (!File::Exists($site_path)) {
				throw new \Exception('Pages file `' . $site_path .
				'` does not exist.');
		}

		$this->requireSitePath($site_path);

		$args_offset = $langs->parseUri($uri);

		$this->parseUri($uri, $args_offset);
	}

	public function addPage($name, $path, $args)
	{
		if (isset($this->pages[$name]))
			throw new \Exception("Page `{$name}` already exists.");

        if ($name === '')
            throw new \Exception("Page name cannot be empty.");

		$this->pagesAliases[$name] = [];

		$this->pages[$name] = new Page($name, $path, $args,
				$this->pagesAliases[$name]);

		return new SitePage($this, $name);
	}

	public function addPageAlias($langName, $pageName, $uri)
	{
		$lang = $this->langs->getLang($langName);
		if ($lang === null)
			throw new \Exception("Language `{$langName}` does not exist.");
		$langName = $lang['name'];

		$page_alias = new PageAlias($uri);

        $this->pagesAliases[$pageName][$lang['name']] = $page_alias;
	}

	private function parseUri(Uri $uri, $args_offset)
	{
		$lang = Langs::Get();
		$langName = $lang['name'];

		$args = $uri->getArgs();
		$args = array_splice($args, $args_offset);

		foreach ($this->pagesAliases as $pageName => $page_aliases) {
			if (!isset($page_aliases[$langName]))
				continue;

			$alias = $page_aliases[$langName];

			$uri_args = $alias->checkUriArgs($args);
			if ($uri_args === null)
				continue;

			$page = $this->pages[$pageName];
			new Args($page->getArgs(), $uri_args);

			$this->currentPageName = $pageName;

			return;
		}

		header('HTTP/1.0 404 Not Found');

		if (isset($this->notFoundPages[$langName])) {
			$this->currentPageName = $this->notFoundPageNames[$langName];
			return;
		}

		throw new \Exception('Page not found.');
	}

	public function setErrorPage($pageName, $langName)
	{
		if (!isset($this->pages[$pageName]))
			throw new \Exception("Page `{$pageName}` does not exist.");

		$lang = $this->langs->getLang($langName);
		$this->errorPageNames[$lang['name']] = $pageName;
	}

	public function setNotFoundPage($pageName, $langName)
	{
		if (!isset($this->pages[$pageName]))
			throw new \Exception("Page `{$pageName}` does not exist.");

		$lang = $this->langs->getLang($langName);
		$this->notFoundPageNames[$lang['name']] = $pageName;
	}

	private function requireSitePath($site_path)
	{
		$eSite = new SitePages($this->langs, $this);

		require($site_path);
	}

}
