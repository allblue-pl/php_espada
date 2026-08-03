<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class Fields { // implements \Iterator
	static public function _($fields = []) {
		return new Fields($fields);
	}


	private $fields;

	public function __construct($fields = []) {
		$this->fields = $fields;
	}

	public function &__get($name) {
		return $this->get($name);
	}

	public function __set($name, $value) {
		$this->fields[$name] = $value;
	}

    public function &get(string $fieldName): mixed {
        if (!in_array($fieldName, array_keys($this->fields))) {
            /** @phpstan-ignore if.alwaysTrue */
			if (EDEBUG)
				Notice::Add("Field `{$fieldName}` not set.");

			$null = null;
			return $null;
		}

		return $this->fields[$fieldName];
    }

	public function getRootFields() {
		return $this->fields;
	}

	public function push($value) {
		$this->fields[] = $value;
	}

	public function set($array) {
		$this->fields = $array;
	}

	public function setSelected($array, $field_names) {
		foreach ($field_names as $field_name) {
			if (!isset($array[$field_name])) {
				Notice::Add("No `{$field_name}` in array.");
				$this->$field_name = null;
				continue;
			}

			$this->$field_name = $array[$field_name];
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
