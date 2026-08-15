<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class Fields { // implements \Iterator
    /**
     * @param array<string, mixed> $fields 
     * @return Fields 
     */
	static public function _(array $fields = []): Fields {
		return new Fields($fields);
	}


    /** @var array<string, mixed> */
	private array $fields;


    /**
     * @param array<string, mixed> $fields 
     * @return void 
     */
	public function __construct(array $fields = []) {
		$this->fields = $fields;
	}

	public function &__get(string $name): mixed {
		return $this->get($name);
	}

	public function __set(string $name, mixed $value): void {
		$this->fields[$name] = $value;
	}

    public function &get(string $fieldName): mixed {
        if (!in_array($fieldName, array_keys($this->fields))) {
			if (EDEBUG)
				Notice::Add("Field `{$fieldName}` not set.");

			$null = null;
			return $null;
		}

		return $this->fields[$fieldName];
    }

    /**
     * @return array<string, mixed> 
     */
	public function _getRootFields(): array {
		return $this->fields;
	}

    /**
     * @param array<string, mixed> $array 
     * @return void 
     */
	public function _set(array $array): void {
		$this->fields = $array;
	}

    /**
     * @param array<string, mixed> $fields 
     * @param list<string> $fieldNames 
     * @return void 
     */
	public function _setSelected(array $fields, array $fieldNames): void {
		foreach ($fieldNames as $fieldName) {
			if (!array_key_exists($fieldName, $fields)) {
				Notice::Add("No `{$fieldName}` in array.");
				$this->$fieldName = null;
				continue;
			}

			$this->$fieldName = $fields[$fieldName];
		}
	}

	// /* Iterable */
	// public function rewind()
    // {
    //     reset($this->fields);
    // }
	//
    // public function current()
    // {
	// 	$field = current($this->fields);
	//
	// 	if (is_array($field))
	// 		return new Fields($field);
	//
    //     return $field;
    // }
	//
    // public function key()
    // {
    //     return key($this->fields);
    // }
	//
    // public function next()
    // {
    //     return next($this->fields);
    // }
	//
    // public function valid()
    // {
    //     $key = key($this->fields);
    //     return ($key !== NULL && $key !== FALSE);
    // }

	// public function get($name)
	// {
	// 	if (!isset($this->fields[$name])) {
	// 		$null = null;
	// 		return $null;
	// 	}
	//
	// 	return $this->fields[$name];
	// }
	//
	// public function set($name, $value)
	// {
	// 	$this->fields[$name] = $value;
	// }
	//
	// public function ref($name, &$value)
	// {
	// 	$this->fields[$name] = &$value;
	// }

}
