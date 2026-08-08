<?php namespace E;
defined("_ESPADA") or die(NO_ACCESS);

/**
 * @phpstan-type T_LangInfo array{
 *   name: string,
 *   alias: string,
 *   code: string,
 *   ltr: bool,
 * }
 */

class Langs {
	static private Langs|null $Instance = null;

    /**
     * @param string $langName 
     * @return T_LangInfo|null
     */
	static public function Get(string $langName = ""): array|null {
        assert(self::$Instance !== null);

		if ($langName === "")
			$langName = self::$Instance->currentLangName;

        if ($langName === null)
            throw new \Exception("Langueage not set.");

		return self::$Instance->getLang($langName);
    }
    
    /**
     * @return array<T_LangInfo>
     */
    static public function GetAll(): array {
        assert(self::$Instance !== null);

        return self::$Instance->langs;
    }

    /**
     * @return list<string>
     */
    static public function GetAllNames(): array {
        $langNames = [];
        foreach (self::GetAll() as $lang) {
            $langNames[] = $lang["name"];
        }

        return $langNames;
    }

    static public function GetName(string $langName = ""): string {
        $langInfo = self::Get($langName);
        if ($langInfo === null)
            throw new \Exception("Language not set.");

        return $langInfo["name"];
    }

    /** @var array<T_LangInfo> */
	private array $langs;
	private string|null $defaultLangName;
	private string|null $currentLangName;

	public function __construct() {
		if (self::$Instance !== null)
			throw new \Exception("\E\Langs already created.");

        $this->langs = [];
        $this->defaultLangName = null;
        $this->currentLangName = null;

		self::$Instance = $this;
	}

	public function add(string $langName, string $langAlias, string $langCode, 
            bool $ltr): void {
        $lang = $this->getLang($langName);
		if ($lang !== null)
			throw new \Exception("Lang `{$langName}` already exists.");

		$this->langs[] = [
			"name" => $langName,
			"alias" => $langAlias,
			"code" => $langCode,
            "ltr" => $ltr,
		];
		if ($this->defaultLangName === null)
			$this->defaultLangName = $langName;
	}

    /**
     * @param string $langName 
     * @return null|T_LangInfo
     */
	public function getLang(string $langName = ""): array|null {
		if ($langName === "")
			$langName = $this->defaultLangName;

        foreach (self::GetAll() as $lang) {
            if ($lang["name"] === $langName)
                return $lang;
        }

		return null;
	}

	// public function getLangPages($lang_name) {
	// 	if (!isset(self::$Instance->langPages[$lang_name]))
	// 		throw new \Exception("Lang `" . $lang_name . "` does not exist.");

	// 	return $this->langPages[$lang_name];
	// }

	public function parseUri(Uri $uri): int {
		$empty_alias_lang_name = null;
		foreach ($this->langs as $lang) {
            $lang_name = $lang["name"];

			if ($lang["alias"] === "")
				$empty_alias_lang_name = $lang_name;
			else if ($uri->getArg(0) === $lang["alias"]) {
				$this->currentLangName = $lang_name;

				return 1;
			}
		}

		if ($empty_alias_lang_name !== null) {
			$this->currentLangName = $empty_alias_lang_name;

			return 0;
		}

		throw new \Exception("Cannot determine language from uri.");
	}

	public function setCurrentLangName(string $langName): void {
		$this->currentLangName = $langName;
	}
}
