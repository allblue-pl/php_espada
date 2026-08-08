<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Holders {
    private Site $site;
    private array $holders;
    private array $holders_Displayed;

    public function __construct(Site $site, array $holders, 
            array &$holders_displayed) {
        $this->site = $site;
        $this->holders = $holders;
        $this->holders_Displayed = &$holders_displayed;
    }

    public function __get(string $name): void {
        $this->view($name);
    }

    public function view(string $holderName): void {
        if (!isset($this->holders[$holderName])) {
            /* @phpstan-ignore if.alwaysTrue */
            if (EDEBUG)
                Notice::Add("Empty holder `{$holderName}`.");

            return;
        }

        if ($this->holders_Displayed[$holderName])
            throw new \Exception("Holder '{$holderName}' already exists.");

        // if ($name === 'postHead') {
        //     print_r($this->holders[$name]);
        //     die;
        // }

        foreach ($this->holders[$holderName] as $layout)
            $layout->display($this->site);
        $this->holders_Displayed[$holderName] = true;
    }
}
