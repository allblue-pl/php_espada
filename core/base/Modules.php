<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Modules {
    /** @var list<Module> */
	private array $modules_Ordered;


	public function __construct() {
		$this->modules_Ordered = [];
	}

	public function add(Module $module): void {
		$this->modules_Ordered[] = $module;
	}

	public function deinitialize(): void {
		$modules_length = count($this->modules_Ordered);
		for ($i = $modules_length - 1; $i >= 0; $i--)
			$this->modules_Ordered[$i]->deinitialize();
	}

	public function postInitialize(Site $site): void {
		$modules_length = count($this->modules_Ordered);
		for ($i = $modules_length - 1; $i >= 0; $i--)
			$this->modules_Ordered[$i]->postInitialize($site);
    }
    
    public function preDisplay(Site $site): void {
        foreach ($this->modules_Ordered as $module)
			$module->preDisplay($site);
    }

	public function preInitialize(Site $site): void {
		foreach ($this->modules_Ordered as $module)
			$module->preInitialize($site);
	}

}
