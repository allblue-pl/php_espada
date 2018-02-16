<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class SitePage
{

    private $name = '';
    private $pages = null;

    public function __construct(\E\Pages $pages, $name)
    {
        $this->pages = $pages;
        $this->name = $name;
    }

    public function alias($uri, $lang_name = '')
    {
        $this->pages->addPageAlias($lang_name, $this->name, $uri);

        return $this;
    }

}
