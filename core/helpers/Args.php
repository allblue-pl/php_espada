<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Args {
	static private ?Args $Instance = null;

	static public function File(string $name): array {
		if (!isset($_FILES[$name]))
			throw new \Exception("File arg `{$name}` does not exist.");

		return $_FILES[$name];
	}

	static public function Get(string $name): string {
		if (!isset($_GET[$name]))
			throw new \Exception("Get arg `{$name}` does not exist.");

		return urldecode($_GET[$name]);
	}

	static public function Get_Exists(string $name): bool {
		return array_key_exists($name, $_GET);
	}

	static public function Get_All(): array {
		$args = [];
		foreach ($_GET as $arg_name => $arg)
			$args[$arg_name] = urldecode($arg);

		return $args;
	}

	static public function Page(string $name): array {
		if (!isset(self::$Instance->pageArgs[$name]))
			throw new \Exception("Page arg `{$name}` does not exist.");

		return self::$Instance->pageArgs[$name];
	}

	static public function Page_Exists(string $name): bool {
		return array_key_exists($name, self::$Instance->pageArgs);
	}

	static public function Page_All(): array {
		return self::$Instance->pageArgs;
	}

	static public function Post(string $name): string {
		if (isset($_POST[$name]))
			return urldecode($_POST[$name]);

		if (isset($_FILES[$name]))
			return urldecode($_FILES[$name]);

        throw new \Exception("Post arg `{$name}` does not exist.");
	}

	static public function Post_All(): array {
		$args = [];
		foreach ($_POST as $arg_name => $arg)
			$args[$arg_name] = $arg;

		foreach ($_FILES as $arg_name => $arg)
			$args[$arg_name] = $arg;

		return $args;
	}

    static public function Post_Exists(string $name): bool {
		if (isset($_POST[$name]))
			return true;

		if (isset($_FILES[$name]))
			return true;

		return false;
	}

	static public function Post_ValidateSize(): bool {
		if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) &&
                empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0)
			return false;

		return true;
	}

	static public function Uri(string $name): array {
		if (!isset(self::$Instance->uriArgs[$name]))
			throw new \Exception("Uri arg `{$name}` does not exist.");

		return self::$Instance->uriArgs[$name];
	}

	static public function Uri_Exists(string $name): bool {
		return isset(self::$Instance->uriArgs[$name]);
	}

	static public function Uri_All(): array {
		return self::$Instance->uriArgs;
	}


    private array $pageArgs;
    private array $uriArgs;

	public function __construct(array $pageArgs, array $uriArgs) {
		if (self::$Instance !== null)
			throw new \Exception('\E\Args already created.');

		self::$Instance = $this;

		$this->pageArgs = $pageArgs;
		$this->uriArgs = $uriArgs;
	}

}
