<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class Layout implements ILayout
{

    static public function _($layout_path, $fields = [])
    {
        return new Layout($layout_path, $fields);
    }

    static private function RequireFile($eFilePath, Holders $eHolders,
            Fields $eFields)
    {
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


    private $filePath = null;
    private $fields = null;

    private $holders = [];
    private $holders_Displayed = [];

    private $validated = false;

    public function __construct($layout_path = null, $fields = [])
    {
        if ($layout_path !== null)
            $this->setPath($layout_path);

        $this->fields = $fields;
    }

    final public function addL($holder_name, Layout $layout)
    {
        // if ($this->postInitialized)
        //     throw new \Exception('Cannot add layout after initialization.');

        if (!isset($this->holders[$holder_name])) {
            $this->holders[$holder_name] = [];
            $this->holders_Displayed[$holder_name] = false;
        }

        $this->holders[$holder_name][] = $layout;

        return $layout;
    }

    final public function display()
    {
        $fields = $this->_getFields();

        $this->validate($fields);

        $holders = new Holders($this->holders, $this->holders_Displayed);

        $fields_array = is_callable($this->fields) ? $fields() : $fields;

        self::RequireFile($this->filePath, $holders,
                Fields::_($fields_array));

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

    final public function &getFields()
    {
        if ($this->validated)
            throw new \Exception('Cannot modify layout after validation.');

        return $this->fields;
    }

    final public function setFields(array $fields)
    {
        if ($this->validated)
            throw new \Exception('Cannot modify layout after validation.');

        $this->fields = array_replace_recursive($this->fields, $fields);
    }

    final public function setPath($layout_path)
    {
        if ($this->validated)
            throw new \Exception('Cannot modify layout after validation.');

        $layout_path_array = explode(':', $layout_path);
        if (count($layout_path_array) !== 2)
            throw new \Exception('Wrong layout path format: ' . $layout_path);

        $this->filePath = Package::Path($layout_path_array[0],
                'layouts/' . $layout_path_array[1] . '.php');
        if ($this->filePath === null)
            throw new \Exception("Layout path `{$layout_path}` does not exist.");
    }

    final public function validate($fields)
    {
        $child_class = get_called_class();

        if ($this->filePath === null)
            throw new \Exception("Layout `path` not set in `{$child_class}`.");

        if ($fields === null)
            throw new \Exception("Layout `fields` not set in {$child_class}.");

        $this->validated = true;
    }

    protected function _getFields()
    {
        return $this->fields;
    }

    private function validateHolders()
    {
        foreach ($this->holders_Displayed as $holder_name => $displayed) {
            if (!$displayed)
                Notice::Add("Holder `$holder_name` set, but not displayed.");
        }
    }

}
