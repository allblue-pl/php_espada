<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class Langs {

	static private $Instance = null;

	static public function Get($lang_name = '')
	{
		if ($lang_name === '')
			$lang_name = self::$Instance->currentLangName;

		return self::$Instance->getLang($lang_name);
    }
    
    static public function GetAll() {
        return self::$Instance->langs;
    }

    static public function GetAllNames() {
        $langNames = [];
        foreach (self::GetAll() as $lang) {
            $langNames[] = $lang['name'];
        }

        return $langNames;
    }

    static public function GetName() {
        return self::Get()['name'];
    }


	private $langs = [];
	private $defaultLangName = null;
	private $currentLangName = null;

	public function __construct()
	{
		if (self::$Instance !== null)
			throw new \Exception("\E\Langs already created.");

		self::$Instance = $this;
	}

	public function add($lang_name, $lang_alias, $lang_code)
	{
        $lang = $this->getLang($lang_name);
		if ($lang !== null)
			throw new \Exception("Lang `{$lang_name}` already exists.");

		$this->langs[] = [
			'name' => $lang_name,
			'alias' => $lang_alias,
			'code' => $lang_code
		];
		if ($this->defaultLangName === null)
			$this->defaultLangName = $lang_name;
	}

	public function getLang($lang_name = '')
	{
		if ($lang_name === '')
			$lang_name = $this->defaultLangName;

        foreach (self::GetAll() as $lang) {
            if ($lang['name'] === $lang_name)
                return $lang;
        }

		return null;
	}

	public function getLangPages($lang_name)
	{
		if (!isset(self::$Instance->langPages[$lang_name]))
			throw new \Exception("Lang `" . $lang_name . '` does not exist.');

		return $this->langPages[$lang_name];
	}

	public function parseUri(\E\Uri $uri)
	{
		$empty_alias_lang_name = null;
		foreach ($this->langs as $lang) {
            $lang_name = $lang['name'];

			if ($lang['alias'] === '')
				$empty_alias_lang_name = $lang_name;
			else if ($uri->getArg(0) === $lang['alias']) {
				$this->currentLangName = $lang_name;

				return 1;
			}
		}

		if ($empty_alias_lang_name !== null) {
			$this->currentLangName = $empty_alias_lang_name;

			return 0;
		}

		throw new \Exception('Cannot determine language from uri.');
	}

	public function setCurrentLangName($lang_name)
	{
		$this->currentLangName = $lang_name;
	}
}
