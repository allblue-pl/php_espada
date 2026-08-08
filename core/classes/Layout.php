<?php namespace E;

use Closure;

defined('_ESPADA') or die(NO_ACCESS);

class Layout implements ILayout {
    static public function _(string $layoutPath, array|Closure $fields = []): Layout {
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
        $fields = $eFields->getRootFields();

        foreach ($fields as $field_name => $field_value) {
            $field_name = '_' . $field_name;
            $$field_name = $field_value;
        }

        unset($fields);
        unset($field_name);
        unset($field_value);

        require($eFilePath);
    }


    private ?string $filePath;
    private array|Closure $fields;

    private array $holders;
    private array $holders_Displayed;

    private bool $validated;

    public function __construct(?string $layoutPath = null, 
            array|Closure $fields = []) {
        if ($layoutPath !== null)
            $this->setPath($layoutPath);

        $this->filePath = null;
        $this->fields = $fields;

        $this->holders = [];
        $this->holders_Displayed = [];

        $this->validated = false;
    }

    final public function addL(string $holderName, Layout $layout): Layout {
        // if ($this->postInitialized)
        //     throw new \Exception('Cannot add layout after initialization.');

        if (!isset($this->holders[$holderName])) {
            $this->holders[$holderName] = [];
            $this->holders_Displayed[$holderName] = false;
        }

        $this->holders[$holderName][] = $layout;

        return $layout;
    }

    final public function display(Site $site): void {
        $this->_preDisplay($site);

        $fields = $this->getFields();

        $this->validate($fields);

        $fieldsArray = is_callable($this->fields) ? $fields() : $fields;
        $fields = Fields::_($fieldsArray);
        $holders = new Holders($site, $this->holders, $this->holders_Displayed);
        $layoutViewer = new LayoutViewer($fields, $holders);

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

    final public function &getFields(): array {
        if ($this->validated)
            throw new \Exception('Cannot modify layout after validation.');

        return $this->fields;
    }

    final public function setFields(array $fields): void {
        if ($this->validated)
            throw new \Exception('Cannot modify layout after validation.');

        $this->fields = array_replace_recursive($this->fields, $fields);
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

    final public function validate(?array $fields): void {
        $child_class = get_called_class();

        if ($this->filePath === null)
            throw new \Exception("Layout `path` not set in `{$child_class}`.");

        if ($fields === null)
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
