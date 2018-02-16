<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Pages
{

	static private $Instance = null;

	static public function Get($page_name = '', $lang_name = '')
	{
		$lang_name = Langs::Get($lang_name)['name'];

		if ($page_name === '')
			$page_name = self::$Instance->currentPageName;

		if (!isset(self::$Instance->pages[$page_name]))
			return null;

		return self::$Instance->pages[$page_name];
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

		$this->pageAliases[$name] = [];

		$this->pages[$name] = new Page($name, $path, $args,
				$this->pagesAliases[$name]);

		return new SitePage($this, $name);
	}

	public function addPageAlias($lang_name, $page_name, $uri)
	{
		$lang = $this->langs->getLang($lang_name);
		if ($lang === null)
			throw new \Exception("Language `{$lang_name}` does not exist.");
		$lang_name = $lang['name'];

		$page_alias = new PageAlias($uri);

		$this->pagesAliases[$page_name][$lang['name']] = $page_alias;
	}

	private function parseUri(Uri $uri, $args_offset)
	{
		$lang = Langs::Get();
		$lang_name = $lang['name'];

		$args = $uri->getArgs();
		$args = array_splice($args, $args_offset);

		foreach ($this->pagesAliases as $page_name => $page_aliases) {
			if (!isset($page_aliases[$lang_name]))
				continue;

			$alias = $page_aliases[$lang_name];

			$uri_args = $alias->checkUriArgs($args);
			if ($uri_args === null)
				continue;

			$page = $this->pages[$page_name];
			new Args($page->getArgs(), $uri_args);

			$this->currentPageName = $page_name;

			return;
		}

		header('HTTP/1.0 404 Not Found');

		if (isset($this->notFoundPages[$lang_name])) {
			$this->currentPageName = $this->notFoundPageNames[$lang_name];
			return;
		}

		throw new \Exception('Page not found.');
	}

	public function setErrorPage($page_name, $lang_name)
	{
		if (!isset($this->pages[$page_name]))
			throw new \Exception("Page `{$page_name}` does not exist.");

		$lang = $this->langs->getLang($lang_name);
		$this->errorPageNames[$lang['name']] = $page_name;
	}

	public function setNotFoundPage($page_name, $lang_name)
	{
		if (!isset($this->pages[$page_name]))
			throw new \Exception("Page `{$page_name}` does not exist.");

		$lang = $this->langs->getLang($lang_name);
		$this->notFoundPageNames[$lang['name']] = $page_name;
	}

	private function requireSitePath($site_path)
	{
		$eSite = new SitePages($this->langs, $this);

		require($site_path);
	}

}
