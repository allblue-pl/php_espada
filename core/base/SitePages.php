<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class SitePages {
    private Langs $langs;
    private Pages $pages;

    public function __construct(Langs $langs, Pages $pages) {
        $this->langs = $langs;
        $this->pages = $pages;
    }

    public function errorPage(string $pageName, string $langName = ''): void {
        $this->pages->setErrorPage($langName, $pageName);
    }

    public function lang(string $name,string $alias, string $code, 
            bool $ltr = true): void {
        $this->langs->add($name, $alias, $code, $ltr);
    }

    public function notFound(string $pageName, array $args = [], 
            string $langName = ''): void {
        $this->pages->setNotFoundPage($langName, $pageName);
    }

    public function page(string $name, string $path, array $args = []): SitePage {
        return $this->pages->addPage($name, $path, $args);
    }
}
