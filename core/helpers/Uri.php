<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Uri
{

	static private $Instance = null;

	static public function Base($pathOnly = true)
	{
		if ($pathOnly)
			return self::$Instance->base;

		return Uri::Domain() . self::$Instance->base;
	}

	static public function Current($pathOnly = true)
	{
		if (self::$Instance === null) {
			throw new \Exception('Cannot get current uri' .
					' before initialization.');
		}

		if ($pathOnly)
			return self::$Instance->uri;

		return Uri::Domain() . self::$Instance->uri;
	}

	static public function Domain()
	{
		if (SITE_DOMAIN !== '')
			return SITE_DOMAIN;

		return $_SERVER['HTTP_HOST'];
	}

	static public function File($path, $pathOnly = true)
	{
		$file_uri = Package::Uri_FromPath($path, 'front', '');
		if ($file_uri === null)
			Notice::Add("Cannot find front file: {$path}.");

		if ($pathOnly)
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
			$langName = '', $pathOnly = true, $includeBase = true)
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

        $uri = '';
        if ($includeBase)
            $uri .= Uri::Base($pathOnly);
        if ($lang['alias'] !== '')
            $uri .= $lang['alias'] . '/';

		return $uri . $pageUri;
    }
    
    static public function Page_Raw($pageName = null, $langName = '', 
            $pathOnly = true, $includeBase = true)
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

        $uri = '';
        if ($includeBase)
            $uri .= Uri::Base($pathOnly);
        if ($lang['alias'] !== '')
            $uri .= $lang['alias'] . '/';

		return $uri . $pageUri;
    }

	static public function Pages($pageNames)
	{
		$uris = [];

		foreach ($pageNames as $key => $pageName)
			$uris[$key] = self::Page($pageName);

		return $uris;
	}

    static public function Protocol()
    {
        if (mb_strpos(self::Domain(), 'https://'))
                return 'https://';

        return 'http://';
    }

	static public function Site($pathOnly = true)
	{
		if ($pathOnly)
			return self::$Instance->uri;

		return self::Domain() . self::$Instance->uri;
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

	public function __construct($uri_Raw)
	{
		if (self::$Instance !== null)
			throw new \Exception("Uri already created.");

		self::$Instance = $this;

		/* Base */
        $uri_Raw = urldecode($uri_Raw);
        $uri = '';
        $allowedChars = 'qwertyuiopasdfghjklzxcvbnm' . 
                'QWERTYUIOPASDFGHJKLZXCVBNM' . 
                '0123456789' .
                '.#?&;/-_' . // url
                '+='; // base64
        for ($i = 0; $i < mb_strlen($uri_Raw); $i++) {
            if (mb_strpos($allowedChars, $uri_Raw[$i]) > -1)
                $uri .= (string)$uri_Raw[$i];
        }
        $this->uri = $uri;

        $this->base = SITE_BASE; // dirname($_SERVER['PHP_SELF']);

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

    public function getUri()
    {
        return $this->uri;
    }

}
