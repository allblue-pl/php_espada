<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class ErrorPage
{

	static private $Title = "Error";
	static private $Message = "Internal Server Error";

	static private $Code = 500;

	static public function Initialize()
	{
		Espada::Deinitialize();

		require(PATH_SITE.'/pages/error.php');
		exit;
	}

	static public function SetTitle($title)
	{
		self::$Title = $title;
	}

	static public function SetMessage($message)
	{
		self::$Message = $message;
	}

	static public function SetCode($code)
	{
		self::$Code = $code;
	}

	static public function GetTitle()
	{
		return self::$Title;
	}

	static public function GetMessage()
	{
		return self::$Message;
	}

	static public function GetCode()
	{
		return self::$Code;
	}

}
