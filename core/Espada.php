<?php defined('_ESPADA') or die(NO_ACCESS);

/* base */
require(__DIR__."/base/ILayout.php");

require(__DIR__."/base/Config.php");
require(__DIR__."/base/ErrorPage.php");
require(__DIR__."/base/Exception.php");
require(__DIR__."/base/Holders.php");
require(__DIR__."/base/Langs.php");
require(__DIR__.'/base/Modules.php');
require(__DIR__.'/base/Notice.php');
require(__DIR__."/base/PageAlias.php");
require(__DIR__."/base/SitePage.php");
require(__DIR__."/base/SitePages.php");

/* classes */
require(__DIR__.'/classes/Layout.php');
require(__DIR__.'/classes/Module.php');
require(__DIR__.'/classes/Site.php');

/* helpers */
require(__DIR__.'/helpers/Args.php');
require(__DIR__."/helpers/Fields.php");
require(__DIR__.'/helpers/File.php');
require(__DIR__.'/helpers/Package.php');
require(__DIR__."/helpers/Page.php");
require(__DIR__."/helpers/Pages.php");
require(__DIR__."/helpers/Path.php");
require(__DIR__."/helpers/Uri.php");


class Espada {

	static private $Instance = null;
	static private $Initialized = false;
	static private $LoadedECoreClasses = [];

	static public function ChangePage($page_name)
	{
		$page = \E\Pages::Get($page_name);
		if ($page === null)
			throw new \Exception("Page `{$page_name}` does not exist.");

		self::SetPage($page);
	}

	static public function Create()
	{
		if (self::$Instance !== null)
			throw new \Exception('Espada already created.');

		new Espada();
	}

	static public function Initialize(\E\Site $site)
	{
		if (PREINIT_CONTENTS !== '')
			E\Notice::Add(PREINIT_CONTENTS);

		if (self::$Instance->site !== null)
			throw new \Exception("Espada already initialized.");

		self::$Initialized = true;

		self::$Instance->site = $site;
		$site->initialize();
	}

	static private function LoadECoreClass($class) {
		if (in_array($class, self::$LoadedECoreClasses))
			return true;

		$class_array = explode('\\', $class);
		$class_array_length = count($class_array);

		if ($class_array_length < 1)
			return false;

		if ($class_array[0] !== 'EC')
			return false;

		$class_array = array_splice($class_array, 1);
		$class_array_length--;

		$add_alias = false;
		if ($class_array_length === 1) {
			$class_array = array(substr($class_array[0], 1), $class_array[0]);
			$class_array_length = 2;
			$add_alias = true;
		}

		$class_path = 'classes';
		for ($i = 1; $i < $class_array_length; $i++)
			$class_path .= '/' . $class_array[$i];
		$class_path .= '.php';

		$class_path = E\Package::Path($class_array[0], $class_path, true);

		if ($class_path === null)
			return false;

		require($class_path);
		self::$LoadedECoreClasses[] = $class;

		if ($add_alias) {
			$full_class_name = 'EC\\' . implode('\\', $class_array);
			class_alias($full_class_name, $class);
			self::$LoadedECoreClasses[] = $full_class_name;
		} else if ($class_array_length === 2){
			if ($class_array[0] === substr($class_array[1], 1)) {
				$short_class_name = 'EC\\' . $class_array[1];
				class_alias($class, $short_class_name);
				self::$LoadedECoreClasses[] = $short_class_name;
			}
		}

		return true;
	}

	// static private function LoadECoreClass($class) {
	// 	if (in_array($class, self::$LoadedClasses))
	// 		return true;
	//
	// 	$class_array = explode('_', $class);
	// 	$class_array_length = count($class_array);
	//
	// 	$add_alias = false;
	// 	if ($class_array_length === 1) {
	// 		$class_array = array(substr($class_array[0], 1), $class_array[0]);
	// 		$class_array_length = 2;
	// 		$add_alias = true;
	// 	}
	//
	// 	$class_path = 'classes/';
	// 	for ($i = 1; $i < $class_array_length - 1; $i++)
	// 		$class_path .= $class_array[$i] . '/';
	// 	$class_path .= implode('_', $class_array) . '.php';
	//
	// 	$class_path = \E\Package::Path($class_array[0], $class_path);
	//
	// 	if ($class_path === null) {
	// 		// throw new \Exception('Class `' . $class . '` does not exist.');
	// 		return false;
	// 	}
	//
	// 	require($class_path);
	// 	self::$LoadedClasses[] = $class;
	//
	// 	if ($add_alias)
	// 		class_alias(implode('_', $class_array), $class);
	//
	// 	return true;
	// }

	static public function NoAccess($message = '')
	{
		self::$Instance->deinitialize();

		header('HTTP/1.0 401 Unauthorized');
		echo $message;
		exit();
	}

	static public function NotFound($message = '')
	{
		self::$Instance->deinitialize();

		header('HTTP/1.0 404 Not Found');
		echo $message;
		exit();
	}

	static public function Redirect($uri, $http_response_code = 303)
	{
		self::$Instance->deinitialize();

		header('Location: ' . $uri, TRUE, $http_response_code);
		exit();
	}

	static private function GetPageFilePath($page_path)
	{
		$page_path_array = explode(':', $page_path);
		if (count($page_path_array) !== 2)
			throw new \Exception('Wrong page file path format:' . $page_path);

		return \E\Package::Path($page_path_array[0],
				'pages/' . $page_path_array[1] . '.php');
	}

	static public function SetPage(\E\Page $page)
	{
		if (self::$Instance !== null)
			self::$Instance->deinitialize();

		require($page->getFilePath());

		self::$Instance->display();
		self::$Instance->deinitialize();

		exit;
	}


	private $pagePath;
	private $site;

	private function __construct()
	{
		self::$Instance = $this;

		set_exception_handler('\E\Exception::ExceptionHandler');
		set_error_handler('\E\Exception::ErrorHandler', E_ALL);
				// E_ERROR | E_WARNING | E_NOTICE);

		spl_autoload_register('Espada::LoadECoreClass');

		if (!\E\File::Exists(PATH_CACHE))
			mkdir(PATH_CACHE, 0700, true);
		if (!\E\File::Exists(PATH_TMP))
			mkdir(PATH_TMP, 0700, true);

		$e_uri = new \E\Uri($_SERVER['REQUEST_URI']);
		if ($e_uri->getUri() !== urldecode($_SERVER['REQUEST_URI'])) {
            header('Location: ' . $e_uri->getUri(), TRUE, 303);
		    exit();
        }

        $e_langs = new \E\Langs();
		$e_pages = new \E\Pages($e_langs, $e_uri);

		/* Page Path */
		self::SetPage(\E\Pages::Get());
	}

	public function deinitialize()
	{
		if ($this->site !== null) {
			$this->site->deinitialize();
			$this->site = null;
		}
	}

	public function display()
	{
		if (self::$Instance->site === null)
			throw new \Exception('\E\Site has not been initialized.');

		// self::$Instance->site->initialize_Fields();

		self::$Instance->site->display();

		// $layout = self::$Instance->site->getRootL();
		// if ($layout === null)
		// 	throw new \Exception('Root layout not set.');
		//
		// $layout->display();
	}

	public function getPagePath()
	{
		return $this->pagePath;
	}

	private function requirePage($page_path)
	{
		require($page_path);
	}

}
