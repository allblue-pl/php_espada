<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Site implements ILayout {
	private bool $preDisplayed = false;
	// private $preInitialized = false;
	private bool $initialized = false;
	// private $postInitialized = false;

	private Modules $siteModules;
	private ?Layout $rootLayout = null;

	private array $holders;

	private array $listeners_PreInitialize;
	private array $listeners_PostInitialize;

    private array $listeners_PreDisplay;


	public function __construct() {
        $this->holders = [];

        $this->listeners_PreInitialize = [];
        $this->listeners_PostInitialize = [];

        $this->listeners_PreDisplay = [];

		$this->siteModules = new Modules();
	}


	final public function addL(string $holderName, Layout $layout): Layout {
		if (!isset($this->holders[$holderName]))
			$this->holders[$holderName] = [];
		$this->holders[$holderName][] = $layout;

		return $layout;
	}

	final public function addLayout(string $holderName, Layout $layout): Layout {
		return $this->addL($holderName, $layout);
	}

    final public function addM(Module $module): Module {
		if ($this->initialized)
			throw new \Exception('Cannot add module after initialization.');

		$this->siteModules->add($module);

		return $module;
	}

	final public function addModule(Module $module): Module {
		return $this->addM($module);
	}

	final public function deinitialize(): void {
		$this->_deinitialize();
		$this->siteModules->deinitialize();
	}

	final public function display(): void {
		if ($this->rootLayout === null)
            throw new \Exception('Root layout not set.');

        $this->_preDisplay();
        if (!$this->preDisplayed)
            throw new \Exception('Parent `_preDisplay` not called.');

        $this->siteModules->preDisplay($this);

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

	final public function initialize(): void {
		/* Pre Initialize */
		$this->siteModules->preInitialize($this);

		$this->_preInitialize();
		foreach ($this->listeners_PreInitialize as $listener)
			$listener($this);

		// $this->preInitialized = true;

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

		// $this->postInitialized = true;
	}

	final public function isInitialized(): bool {
		return $this->initialized;
	}

	// final public function layouts() {
	// 	if ($this->rootLayout === null)
	// 		throw new \Exception('Root layout not set.');

	// 	return $this->rootLayout->layouts;
	// }

	final public function onPostInitialize(\Closure $listener): void {
        if ($this->initialized) {
            throw new \Exception("Cannot add 'PostInitialize' listener after initialization.");
        }

		$this->listeners_PostInitialize[] = $listener;
	}

    final public function onPreDisplay(\Closure $listener): void {
        if ($this->preDisplayed) {
            throw new \Exception("Cannot add 'PreDisplay' listener after displaying.");
        }

		$this->listeners_PreDisplay[] = $listener;
	}

	final public function onPreInitialize(\Closure $listener): void {
        if ($this->initialized) {
            throw new \Exception("Cannot add 'PreInitialize' listener after initialization.");
        }

		$this->listeners_PreInitialize[] = $listener;
	}

	final public function setRootL(Layout $layout): void {
		$this->rootLayout = $layout;
	}

	
	protected function _deinitialize(): void {

	}

	protected function _initialize(): void {
		$this->initialized = true;
	}

	protected function _postInitialize(): void {
		
	}

	protected function _preDisplay(): void {
		$this->preDisplayed = true;
	}

	protected function _preInitialize(): void {

	}

}
