<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class SitePages
{

    private $langs = null;
    private $pages = null;

    public function __construct(\E\Langs $langs, \E\Pages $pages)
    {
        $this->langs = $langs;
        $this->pages = $pages;
    }

    public function errorPage($page_name, $args = [], $lang_name = '')
    {
        $this->pages->setErrorPage($lang_name, $page_name);
    }

    public function lang($name, $alias, $code, $ltr = true)
    {
        $this->langs->add($name, $alias, $code, $ltr);
    }

    public function notFound($page_name, $args = [], $lang_name = '')
    {
        $this->pages->setNotFoundPage($lang_name, $page_name);
    }

    public function page($name, $path, $args = [])
    {
        return $this->pages->addPage($name, $path, $args);
    }

}
