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

    public function alias($arg1, $arg2 = null)
    {
        $langName = null;
        $uri = null;
        if ($arg2 === null) {
            $langName = '';
            $uri = $arg1;
        } else {
            $langName = $arg1;
            $uri = $arg2;
        }

        if ($langName === '*') {
            $langs = Langs::GetAll();
            foreach ($langs as $lang)
                $this->pages->addPageAlias($lang['name'], $this->name, $uri);
        } else
            $this->pages->addPageAlias($langName, $this->name, $uri);

        return $this;
    }

}
