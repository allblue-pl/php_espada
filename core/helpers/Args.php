<?php namespace E;

use Exception;

defined('_ESPADA') or die(NO_ACCESS);

/**
 * @phpstan-import-type T_PageArgs from Page
 * @phpstan-import-type T_UriArgs from PageAlias
 * @package E
 */

class Args {
	static private Args|null $Instance = null;

    /**
     * @param string $name 
     * @return mixed 
     * @throws Exception 
     */
	static public function File(string $name): mixed {
		if (!isset($_FILES[$name]))
			throw new \Exception("File arg `{$name}` does not exist.");

		return $_FILES[$name];
	}

	static public function Get(string $name): string {
		if (!isset($_GET[$name]))
			throw new \Exception("Get arg `{$name}` does not exist.");

        /** @var string */
        $getArg = $_GET[$name];

		return urldecode($getArg);
	}

	static public function Get_Exists(string $name): bool {
		return array_key_exists($name, $_GET);
	}

    /**
     * @return array<string, string>
     */
	static public function Get_All(): array {
		$args = [];
		foreach ($_GET as $argName => $arg) {
            /** @var string $argName */
            /** @var string $arg */
			$args[$argName] = urldecode($arg);
        }

		return $args;
	}

	static public function Page(string $name): mixed {
		if (!isset(self::$Instance->pageArgs[$name]))
			throw new \Exception("Page arg `{$name}` not set.");

		return self::$Instance->pageArgs[$name];
	}

	static public function Page_Exists(string $name): bool {
        assert(self::$Instance !== null);

		return array_key_exists($name, self::$Instance->pageArgs);
	}

    /**
     * @return T_PageArgs
     */
	static public function Page_All(): array {
        assert(self::$Instance !== null);

		return self::$Instance->pageArgs;
	}

	static public function Post(string $name): string {
		if (isset($_POST[$name])) {
            /** @var string */
            $postArg = $_POST[$name];
			return urldecode($postArg);
        }

		if (isset($_FILES[$name])) {
            /** @var string */
            $fileArg = $_FILES[$name];
			return urldecode($fileArg);
        }

        throw new \Exception("Post arg `{$name}` does not exist.");
	}

    /**
     * @return array<string, string> 
     */
	static public function Post_All(): array {
		$args = [];
		foreach ($_POST as $argName => $arg) {
            /** @var string $argName */
            /** @var string $arg */
			$args[$argName] = urldecode($arg);
        }

		foreach ($_FILES as $argName => $arg) {
            /** @var string $argName */
            /** @var string $arg */
			$args[$argName] = urldecode($arg);
        }

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

	static public function Uri(string $name): string {
		if (!isset(self::$Instance->uriArgs[$name]))
			throw new \Exception("Uri arg `{$name}` does not exist.");

		return self::$Instance->uriArgs["args"][$name];
	}

    /**
     * @return list<string> 
     */
    static public function Uri_Extra(): array {
        assert(self::$Instance !== null);

        return self::$Instance->uriArgs["extra"];
    }

	static public function Uri_Exists(string $name): bool {
        assert(self::$Instance !== null);

		return isset(self::$Instance->uriArgs[$name]);
	}

    /**
     * @return T_UriArgs
     */
	static public function Uri_All(): array {
        assert(self::$Instance !== null);

		return self::$Instance->uriArgs;
	}


    /** @var T_PageArgs */
    private array $pageArgs;
    /** @var T_UriArgs */
    private array $uriArgs;

    /**
     * 
     * @param T_PageArgs $pageArgs 
     * @param T_UriArgs $uriArgs 
     * @return void 
     * @throws Exception 
     */
	public function __construct(array $pageArgs, array $uriArgs) {
		if (self::$Instance !== null)
			throw new \Exception('\E\Args already created.');

		self::$Instance = $this;

		$this->pageArgs = $pageArgs;
		$this->uriArgs = $uriArgs;
	}

}
