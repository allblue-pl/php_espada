<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

/**
 * @phpstan-type T_HolderLayoutsArr array<string, list<Layout>>
 */

class Holders {
    private Site $site;
    /** @var T_HolderLayoutsArr */
    private array $holderLayoutsArr;
    /** @var array<string, bool> */
    private array $holders_Displayed;

    /**
     * 
     * @param Site $site 
     * @param T_HolderLayoutsArr $holderLayoutsArr 
     * @param array<string, bool> &$holders_displayed 
     * @return void 
     */
    public function __construct(Site $site, array $holderLayoutsArr, 
            array &$holders_displayed) {
        $this->site = $site;
        $this->holderLayoutsArr = $holderLayoutsArr;
        $this->holders_Displayed = &$holders_displayed;
    }

    public function __get(string $name): void {
        $this->view($name);
    }

    public function view(string $holderName): void {
        if (!isset($this->holderLayoutsArr[$holderName])) {
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

        foreach ($this->holderLayoutsArr[$holderName] as $layout)
            $layout->display($this->site);
        $this->holders_Displayed[$holderName] = true;
    }
}
