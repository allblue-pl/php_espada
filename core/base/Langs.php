<?php namespace E;
defined("_ESPADA") or die(NO_ACCESS);

class Langs {
	static private ?Langs $Instance = null;

	static public function Get(string $langName = ""): ?array {
		if ($langName === "")
			$langName = self::$Instance->currentLangName;

		return self::$Instance->getLang($langName);
    }
    
    static public function GetAll(): array {
        return self::$Instance->langs;
    }

    static public function GetAllNames(): array {
        $langNames = [];
        foreach (self::GetAll() as $lang) {
            $langNames[] = $lang["name"];
        }

        return $langNames;
    }

    static public function GetName(): string {
        return self::Get()["name"];
    }


	private array $langs;
	private ?string $defaultLangName;
	private ?string $currentLangName;

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

	public function getLang(string $langName = ""): ?array {
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
