<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Modules
{

	private $modules = null;
	private $modules_Ordered = null;

	public function __construct()
	{
		$this->modules = [];
		$this->modules_Ordered = [];
	}

	public function __get($name)
	{
		if (!isset($this->modules[$name]))
			throw new \Exception("Module `{$name}` does not exist.");

		return $this->modules[$name];
	}

	public function add($module_name, Module $module)
	{
		if (isset($this->modules[$module_name]))
			throw new \Exception("Module `{$module_name}` already exists.");

		$this->modules[$module_name] = $module;
		$this->modules_Ordered[] = $module;
	}

	public function deinitialize()
	{
		$modules_length = count($this->modules_Ordered);
		for ($i = $modules_length - 1; $i >= 0; $i--)
			$this->modules_Ordered[$i]->deinitialize();
	}

	public function postInitialize(Site $site)
	{
		$modules_length = count($this->modules_Ordered);
		for ($i = $modules_length - 1; $i >= 0; $i--)
			$this->modules_Ordered[$i]->postInitialize($site);
	}

	public function preInitialize(Site $site)
	{
		foreach ($this->modules_Ordered as $module)
			$module->preInitialize($site);
	}

}
