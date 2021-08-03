<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Site implements ILayout
{

	private $preDisplayed = false;
	private $preInitialized = false;
	private $initialized = false;
	private $postInitialized = false;

	private $siteModules = null;
	private $rootLayout = null;

	private $holders = [];

	private $listeners_PreInitialize = [];
	private $listeners_PostInitialize = [];

    private $listeners_PreDisplay = [];


	public function __construct()
	{
		$this->siteModules = new Modules();
	}


	final public function __get($name)
	{
		if ($name === 'modules' || $name === 'm')
			return $this->siteModules;

		throw new \Exception("Site property `{$name}` does not exit.");
		return null;
    }


	final public function addL($holder_name, Layout $layout)
	{
		if (!isset($this->holders[$holder_name]))
			$this->holders[$holder_name] = [];
		$this->holders[$holder_name][] = $layout;

		return $layout;
	}

	final public function addLayout($holder_name, Layout $layout)
	{
		$this->addL($holder_name, $layout);
	}

	final public function addM($module_name, Module $module)
	{
		if ($this->initialized)
			throw new \Exception('Cannot add module after initialization.');

		$this->siteModules->add($module_name, $module);

		return $module;
	}

	final public function addModule($module_name, Module $module)
	{
		$this->addM($module_name, $module);
	}

	final public function deinitialize()
	{
		$this->_deinitialize();
		$this->siteModules->deinitialize();
	}

	final public function display()
	{
		$this->_preDisplay();
		if (!$this->preDisplayed)
			throw new \Exception('Parent `_preDisplay` not called.');

		if ($this->rootLayout === null)
            throw new \Exception('Root layout not set.');

        $this->siteModules->preDisplay($this);

        // echo "tutaj?" . count($this->listeners_PreDisplay);
        // die;
        foreach ($this->listeners_PreDisplay as $listener)
            $listener($this);

		foreach ($this->holders as $holder_name => $layouts) {
			foreach ($layouts as $l) {
				$this->rootLayout->addL($holder_name, $l);
			}
		}

		$this->rootLayout->display($this);
	}

	// final public function getRootL()
	// {
	// 	return $this->rootLayout;
	// }

	final public function initialize()
	{
		/* Pre Initialize */
		$this->siteModules->preInitialize($this);

		$this->_preInitialize();
		foreach ($this->listeners_PreInitialize as $listener)
			$listener($this);

		$this->preInitialized = true;

		/* Initialized */
		$this->_initialize();
		if (!$this->initialized)
			throw new \Exception('Parent `_initialize` not called.');

		/* Post Initialize */
		for ($i = count($this->listeners_PostInitialize) - 1; $i >=0; $i--) {
            $this->listeners_PostInitialize[$i]($this);
        }
		$this->_postInitialize();

		$this->siteModules->postInitialize($this);

		$this->postInitialized = true;
	}

	final public function isInitialized()
	{
		return $this->initialized;
	}

	final public function layouts()
	{
		if ($this->rootLayout === null)
			throw new \Exception('Root layout not set.');

		return $this->rootLayout->layouts;
	}

	final public function onPostInitialize(\Closure $listener)
	{
        if ($this->initialized) {
            throw new \Exception("Cannot add 'PostInitialize' listener after initialization.");
        }

		$this->listeners_PostInitialize[] = $listener;
	}

    final public function onPreDisplay(\Closure $listener)
	{
        if ($this->preDisplayed) {
            throw new \Exception("Cannot add 'PreDisplay' listener after displaying.");
        }

		$this->listeners_PreDisplay[] = $listener;
	}

	final public function onPreInitialize(\Closure $listener)
	{
        if ($this->initialized) {
            throw new \Exception("Cannot add 'PreInitialize' listener after initialization.");
        }

		$this->listeners_PreInitialize[] = $listener;
	}

	final public function setRootL(Layout $layout)
	{
		$this->rootLayout = $layout;
	}

	
	protected function _deinitialize()
	{

	}

	protected function _initialize()
	{
		$this->initialized = true;
	}

	protected function _postInitialize()
	{
		
	}

	protected function _preDisplay()
	{
		$this->preDisplayed = true;
	}

	protected function _preInitialize()
	{

	}

}
