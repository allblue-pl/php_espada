<?php namespace E;

use Exception;

defined('_ESPADA') or die(NO_ACCESS);

/**
 * @phpstan-type T_LayoutFields array<string, mixed>|\Closure(): array<string, mixed>
 */

class Layout implements ILayout {
    /**
     * @param string $layoutPath 
     * @param null|T_LayoutFields $fields 
     * @return Layout 
     */
    static public function _(string $layoutPath, array|\Closure|null $fields = null): Layout {
        return new Layout($layoutPath, $fields);
    }

    static public function Exists(string $layoutPath): bool {
        $layoutPath_array = explode(':', $layoutPath);
        if (count($layoutPath_array) !== 2)
            return false;

        $filePath = Package::Path($layoutPath_array[0],
                'layouts/' . $layoutPath_array[1] . '.php');
        if ($filePath === null)
            return false;

        return true;
    }

    static private function RequireFile(string $eFilePath, LayoutViewer $l, 
            Holders $eHolders, Fields $eFields): void {
        $fields = $eFields->_getRootFields();

        foreach ($fields as $field_name => $field_value) {
            $field_name = '_' . $field_name;
            $$field_name = $field_value;
        }

        unset($fields);
        unset($field_name);
        unset($field_value);

        require($eFilePath);
    }


    private string|null $filePath;
    /** @var T_LayoutFields|null */
    private array|\Closure|null $fields;

    /** @var array<string, list<Layout>> */
    private array $holderLayouts;
    /** @var array<string, bool> */
    private array $holders_Displayed;

    private bool $validated;

    /**
     * 
     * @param null|string $layoutPath 
     * @param T_LayoutFields|null $fields 
     * @return void 
     */
    public function __construct(string|null $layoutPath = null, 
            array|\Closure|null $fields = []) {
        if ($layoutPath !== null)
            $this->setPath($layoutPath);

        $this->filePath = null;
        $this->fields = $fields;

        $this->holderLayouts = [];
        $this->holders_Displayed = [];

        $this->validated = false;
    }

    final public function addL(string $holderName, Layout $layout): Layout {
        // if ($this->postInitialized)
        //     throw new \Exception('Cannot add layout after initialization.');

        if (!isset($this->holderLayouts[$holderName])) {
            $this->holderLayouts[$holderName] = [];
            $this->holders_Displayed[$holderName] = false;
        }

        $this->holderLayouts[$holderName][] = $layout;

        return $layout;
    }

    final public function display(Site $site): void {
        $this->_preDisplay($site);

        $fields = &$this->fields;

        $this->validate();
        assert($fields !== null);

        if ($fields instanceof \Closure) {
            /**   */
            $fieldsArray = $fields();
        } else
            $fieldsArray = $fields;

        $fields = Fields::_($fieldsArray);
        $holders = new Holders($site, $this->holderLayouts, $this->holders_Displayed);
        $layoutViewer = new LayoutViewer($fields, $holders);

        if ($this->filePath === null) {
            $childClass = get_called_class();
            throw new \Exception("File path not set in layout: '{$childClass}'");
        }

        self::RequireFile($this->filePath, $layoutViewer, $holders, $fields);

        /** @phpstan-ignore if.alwaysTrue */
        if (EDEBUG)
            $this->validateHolders();
    }

    // public function preInitialize()
    // {
    //     $this->_preInitialize();
    //
    //     foreach ($this->holders as $layouts)
    //         foreach ($layouts as $layout)
    //             $layout->preInitialize();
    // }

    /**
     * @return array<string, mixed>
     * @throws Exception 
     */
    final public function &getFields(): array {
        if ($this->fields === null)
            throw new \Exception("Fields not set.");

        if ($this->fields instanceof \Closure)
            throw new \Exception("Cannot get fields of 'Closure' type.");

        if ($this->validated)
            throw new \Exception('Cannot modify layout after validation.');

        return $this->fields;
    }

    /**
     * 
     * @param array<string, mixed> $fields 
     * @return void 
     * @throws Exception 
     */
    final public function setFields(array $fields): void {
        if ($this->fields === null)
             throw new \Exception("Fields not set.");

        if ($this->fields instanceof \Closure)
            throw new \Exception("Cannot set fields of 'Closure' type.");

        if ($this->validated)
            throw new \Exception('Cannot modify layout after validation.');

        /** @var array<string, mixed> */
        $fields_New = array_replace_recursive($this->fields, $fields);

        $this->fields = $fields_New;
    }

    final public function setPath(string $layoutPath): void {
        if ($this->validated)
            throw new \Exception('Cannot modify layout after validation.');

        $layoutPath_array = explode(':', $layoutPath);
        if (count($layoutPath_array) !== 2)
            throw new \Exception('Wrong layout path format: ' . $layoutPath);

        $this->filePath = Package::Path($layoutPath_array[0],
                'layouts/' . $layoutPath_array[1] . '.php');
        if ($this->filePath === null)
            throw new \Exception("Layout path `{$layoutPath}` does not exist.");
    }

    final public function validate(): void {
        $child_class = get_called_class();

        if ($this->filePath === null)
            throw new \Exception("Layout `path` not set in `{$child_class}`.");

        if ($this->fields === null)
            throw new \Exception("Layout `fields` not set in {$child_class}.");

        $this->validated = true;
    }


    protected function _preDisplay(Site $site): void {

    }


    private function validateHolders(): void {
        foreach ($this->holders_Displayed as $holder_name => $displayed) {
            if (!$displayed)
                Notice::Add("Holder `$holder_name` set, but not displayed.");
        }
    }
}
