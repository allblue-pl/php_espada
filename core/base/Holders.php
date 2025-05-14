<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Holders {

    private $site = null;
    private $holders = [];
    private $holders_Displayed = [];

    public function __construct(Site $site, $holders, &$holders_displayed)
    {
        $this->site = $site;
        $this->holders = $holders;
        $this->holders_Displayed = &$holders_displayed;
    }

    public function __get($name)
    {
        if (!isset($this->holders[$name])) {
            if (EDEBUG)
                Notice::Add("Empty holder `{$name}`.");

            return;
        }

        if ($this->holders_Displayed[$name])
            throw new \Exception("Holder '{$name}' already exists.");

        // if ($name === 'postHead') {
        //     print_r($this->holders[$name]);
        //     die;
        // }

        foreach ($this->holders[$name] as $layout)
            $layout->display($this->site);
        $this->holders_Displayed[$name] = true;
    }

}
