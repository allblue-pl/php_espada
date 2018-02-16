<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


abstract class Module
{

	private $outputs = [];
	private $initializations = [];

	private $preInitialized = false;
	private $postInitialized = false;

	public function __construct()
	{

	}

	final public function preInitialize(\E\Site $site)
	{
		if (!$this->preInitialized) {
			$this->_preInitialize($site);

			$this->preInitialized = true;
		}
	}

	final public function postInitialize(\E\Site $site)
	{
		if (!$this->postInitialized) {
			$this->_postInitialize($site);

			$this->postInitialized = true;
		}
	}

	final public function deinitialize()
	{
		$this->_deinitialize();
	}

	public function isInitialized()
	{
		return $this->preInitialized;
	}

	public function requireBeforePostInitialize()
	{
		if ($this->postInitialized)
			throw new \Exception('Can`t execute after post initialize.');
	}

	public function requirePostInitialize()
	{
		if (!$this->postInitialized)
			throw new \Exception('Post initialization required.');
	}

	public function requirePreInitialize()
	{
		if (!$this->preInitialized)
			throw new \Exception('Pre initialization required.');
	}

	protected function _preInitialize(\E\Site $site)
	{

	}

	protected function _postInitialize(\E\Site $site)
	{

	}

	protected function _deinitialize()
	{

	}

}
