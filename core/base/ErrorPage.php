<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class ErrorPage {

	static private string$Title = "Error";
	static private string $Message = "Internal Server Error";

	static private int $Code = 500;

	static public function Initialize(): void {
		\Espada::Deinitialize();

		require(PATH_SITE.'/pages/error.php');
		exit;
	}

	static public function SetTitle(string $title): void {
		self::$Title = $title;
	}

	static public function SetMessage(string $message): void {
		self::$Message = $message;
	}

	static public function SetCode(int $code): void {
		self::$Code = $code;
	}

	static public function GetTitle(): string {
		return self::$Title;
	}

	static public function GetMessage(): string {
		return self::$Message;
	}

	static public function GetCode(): int {
		return self::$Code;
	}

}
