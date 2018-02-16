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

	static public function Page($page_name = null, $uri_args = null,
			$lang_name = '', $path_only = true)
	{
		if ($page_name === null) {
			$page_name = Pages::Get()->getName();

			if ($uri_args === null)
				$uri_args = Args::Uri_All();
		}

		if ($uri_args === null)
			$uri_args = [];

		$page = Pages::Get($page_name);

		if ($page === null)
			throw new \Exception("Page `{$page_name}` does not exist.");

		$lang = Langs::Get($lang_name);
		if ($lang === null)
			throw new \Exception("Lang `{$lang_name}` does not exist.");

		$lang_name = $lang['name'];

		$page_uri = $page->getUri($uri_args, $lang_name);

		return Uri::Base($path_only) . $page_uri;
	}

	static public function Pages($page_names)
	{
		$uris = [];

		foreach ($page_names as $key => $page_name)
			$uris[$key] = self::Page($page_name);

		return $uris;
	}

	static public function Site($path_only = true)
	{
		if ($path_only)
			return self::$Instance->uri;

		return self::GetDomain() . self::$Instance->uri;
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
