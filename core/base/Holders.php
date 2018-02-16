<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Holders
{

    private $holders = [];
    private $holders_Displayed = [];

    public function __construct($holders, &$holders_displayed)
    {
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

        foreach ($this->holders[$name] as $layout)
            $layout->display();
        $this->holders_Displayed[$name] = true;
    }

}
