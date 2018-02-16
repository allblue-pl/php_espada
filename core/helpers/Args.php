<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Args
{

	static private $Instance = null;

	static public function File($name)
	{
		if (!isset($_FILES[$name]))
			throw new \Exception("File arg `{$name}` does not exist.");

		return $_FILES[$name];
	}

	static public function Get($name)
	{
		if (!isset($_GET[$name]))
			throw new \Exception("Get arg `{$name}` does not exist.");

		return urldecode($_GET[$name]);
	}

	static public function Get_Exists($name)
	{
		return array_key_exists($name, $_GET);
	}

	static public function Get_All()
	{
		$args = [];
		foreach ($_GET as $arg_name => $arg)
			$args[$arg_name] = urldecode($arg);

		return $args;
	}

	static public function Page($name)
	{
		if (!isset(self::$Instance->pageArgs[$name]))
			throw new \Exception("Page arg `{$name}` does not exist.");

		return self::$Instance->pageArgs[$name];
	}

	static public function Page_Exists($name)
	{
		return array_key_exists($name, self::$Instance->pageArgs);
	}

	static public function Page_All()
	{
		return self::$Instance->pageArgs;
	}

	static public function Post($name, $default = null)
	{
		if (isset($_POST[$name]))
			return urldecode($_POST[$name]);

		if (isset($_FILES[$name]))
			return urldecode($_FILES[$name]);

		if (EDEBUG)
			return self::Get($name, $default);

		return $default;
	}

	static public function Post_All()
	{
		$args = [];
		foreach ($_POST as $arg_name => $arg)
			$args[$arg_name] = urldecode($arg);

		foreach ($_FILES as $arg_name => $arg)
			$args[$arg_name] = $arg;

		return $args;
	}

	static public function Post_ValidateSize()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) &&
                empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0)
			return false;

		return true;
	}

	static public function Uri($name)
	{
		if (!isset(self::$Instance->uriArgs[$name]))
			throw new \Exception("Uri arg `{$name}` does not exist.");

		return self::$Instance->uriArgs[$name];
	}

	static public function Uri_Exists($name)
	{
		return isset(self::$Instance->uriArgs[$name]);
	}

	static public function Uri_All()
	{
		return self::$Instance->uriArgs;
	}


	private $pageUriArgs = null;

	public function __construct($page_args, $uri_args)
	{
		if (self::$Instance !== null)
			throw new \Exception('\E\Args already created.');

		self::$Instance = $this;

		$this->pageArgs = $page_args;
		$this->uriArgs = $uri_args;
	}

}
