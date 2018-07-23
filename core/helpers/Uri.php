<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Uri
{

	static private $Instance = null;

	static public function Base($path_only = true)
	{
		if ($path_only)
			return self::$Instance->base;

		return Uri::Domain() . self::$Instance->base;
	}

	static public function Current($path_only = true)
	{
		if (self::$Instance === null) {
			throw new \Exception('Cannot get current uri' .
					' before initialization.');
		}

		if ($path_only)
			return self::$Instance->uri;

		return Uri::Domain() . self::$Instance->uri;
	}

	static public function Domain()
	{
		if (SITE_DOMAIN !== '')
			return SITE_DOMAIN;

		return $_SERVER['HTTP_HOST'];
	}

	static public function File($path, $path_only = true)
	{
		$file_uri = Package::Uri_FromPath($path, 'front', '');
		if ($file_uri === null)
			Notice::Add("Cannot find front file: {$path}.");

		if ($path_only)
			return $file_uri;

		return self::Domain() . $file_uri;
	}

	static public function Media($package_name, $file_path)
	{
		$package_name = mb_strtolower($package_name);
		$fs_file_path = PATH_MEDIA . '/' . $package_name . '/' . $file_path;

		if (!file_exists($fs_file_path))
			return null;

		return URI_MEDIA . $package_name . '/' . $file_path;
	}

	static public function Page($pageName = null, $uriArgs = null,
			$langName = '', $path_only = true)
	{
		if ($pageName === null) {
			$pageName = Pages::Get()->getName();

			if ($uriArgs === null)
				$uriArgs = Args::Uri_All();
		}

		if ($uriArgs === null)
			$uriArgs = [];

		$page = Pages::Get($pageName);

		if ($page === null)
			throw new \Exception("Page `{$pageName}` does not exist.");

		$lang = Langs::Get($langName);
		if ($lang === null)
			throw new \Exception("Lang `{$langName}` does not exist.");

		$langName = $lang['name'];

		$pageUri = $page->getAlias($uriArgs, $langName);

		return Uri::Base($path_only) . $pageUri;
    }
    
    static public function Page_Raw($pageName = null, $langName = '', 
            $path_only = true)
    {
        if ($pageName === null)
			$pageName = Pages::Get()->getName();

		$page = Pages::Get($pageName);

		if ($page === null)
			throw new \Exception("Page `{$pageName}` does not exist.");

		$lang = Langs::Get($langName);
		if ($lang === null)
			throw new \Exception("Lang `{$langName}` does not exist.");

		$langName = $lang['name'];

		$pageUri = $page->getAlias_Raw($langName);

		return Uri::Base($path_only) . $pageUri;
    }

	static public function Pages($pageNames)
	{
		$uris = [];

		foreach ($pageNames as $key => $pageName)
			$uris[$key] = self::Page($pageName);

		return $uris;
	}

	static public function Site($path_only = true)
	{
		if ($path_only)
			return self::$Instance->uri;

		return self::GetDomain() . self::$Instance->uri;
    }
    
    static public function Query($getArgs)
    {
        $query = '';

        $first = true;
        foreach ($getArgs as $argName => $argValue) {
            $query .= ($first ? '?' : '&') . $argName . '=' . urlencode($argValue);
            $first = false;
        }

        return $query;
    }


	private $base;
	private $args;
	private $uri;

	public function __construct($uri)
	{
		if (self::$Instance !== null)
			throw new \Exception("Uri already created.");

		self::$Instance = $this;

		/* Base */
		$uri = urldecode($uri);
		$this->uri = $uri;

		$this->base = dirname($_SERVER['PHP_SELF']);

		if ($this->base === '' || $this->base === '\\')
			$this->base = '/';
		else if ($this->base[mb_strlen($this->base) - 1] !== '/')
			$this->base = $this->base . '/';

		/* Uri Args */
		$uri = substr($uri, mb_strlen($this->base));
		$uri = explode('?', $uri)[0];
		$this->args = explode('/', $uri);
		if ($this->args[count($this->args) - 1] === '')
			array_pop($this->args);
	}

	public function getArg($index)
	{
		if (isset($this->args[$index]))
			return $this->args[$index];

		return null;
	}

	public function getArgs()
	{
		return $this->args;
	}

	public function getArgs_Length()
	{
		return count($this->args);
	}

}
