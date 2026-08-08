<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


abstract class Module {
	// private $outputs = [];
	// private $initializations = [];

    private bool $preDisplayed;
	private bool $preInitialized;
	private bool $postInitialized;

	public function __construct(Site $site) {
        $this->preDisplayed = false;
        $this->preInitialized = false;
        $this->postInitialized = false;

        $site->addM($this);
	}

    final public function preDisplay(Site $site): void {
        if (!$this->preDisplayed) {
			$this->_preDisplay($site);

			$this->preDisplayed = true;
		}
    }

	final public function preInitialize(Site $site): void {
		if (!$this->preInitialized) {
			$this->_preInitialize($site);

			$this->preInitialized = true;
		}
	}

	final public function postInitialize(Site $site): void {
		if (!$this->postInitialized) {
			$this->_postInitialize($site);

			$this->postInitialized = true;
		}
	}

	final public function deinitialize(): void {
		$this->_deinitialize();
	}

	public function isInitialized(): bool {
		return $this->preInitialized;
	}

	public function requireBeforePostInitialize(): void {
		if ($this->postInitialized)
			throw new \Exception('Can`t execute after post initialize.');
	}

	public function requirePostInitialize(): void {
		if (!$this->postInitialized)
			throw new \Exception('Post initialization required.');
	}

    public function requireBeforePreDisplay(): void {
		if ($this->preDisplayed)
			throw new \Exception('Can`t execute after `preDisplayed`.');
	}

	public function requirePreInitialize(): void {
		if (!$this->preInitialized)
			throw new \Exception('Pre initialization required.');
	}

    protected function _preDisplay(Site $site): void {

    }

	protected function _preInitialize(Site $site): void {

	}

	protected function _postInitialize(Site $site): void {

	}

	protected function _deinitialize(): void {

	}
}
