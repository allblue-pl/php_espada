<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


abstract class Module {

	private $outputs = [];
	private $initializations = [];

    private $preDisplayed = false;
	private $preInitialized = false;
	private $postInitialized = false;

	public function __construct() {

	}

    final public function preDisplay(Site $site) {
        if (!$this->preDisplayed) {
			$this->_preDisplay($site);

			$this->preDisplayed = true;
		}
    }

	final public function preInitialize(Site $site) {
		if (!$this->preInitialized) {
			$this->_preInitialize($site);

			$this->preInitialized = true;
		}
	}

	final public function postInitialize(Site $site) {
		if (!$this->postInitialized) {
			$this->_postInitialize($site);

			$this->postInitialized = true;
		}
	}

	final public function deinitialize() {
		$this->_deinitialize();
	}

	public function isInitialized() {
		return $this->preInitialized;
	}

	public function requireBeforePostInitialize() {
		if ($this->postInitialized)
			throw new \Exception('Can`t execute after post initialize.');
	}

	public function requirePostInitialize() {
		if (!$this->postInitialized)
			throw new \Exception('Post initialization required.');
	}

    public function requireBeforePreDisplay() {
		if ($this->preDisplayed)
			throw new \Exception('Can`t execute after `preDisplayed`.');
	}

	public function requirePreInitialize() {
		if (!$this->preInitialized)
			throw new \Exception('Pre initialization required.');
	}

    protected function _preDisplay(Site $site) {

    }

	protected function _preInitialize(Site $site) {

	}

	protected function _postInitialize(Site $site) {

	}

	protected function _deinitialize() {

	}

}
