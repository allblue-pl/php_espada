<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class Fields { // implements \Iterator
	static public function _(array $fields = []): Fields {
		return new Fields($fields);
	}


	private array $fields;

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
            /** @phpstan-ignore if.alwaysTrue */
			if (EDEBUG)
				Notice::Add("Field `{$fieldName}` not set.");

			$null = null;
			return $null;
		}

		return $this->fields[$fieldName];
    }

	public function getRootFields(): array {
		return $this->fields;
	}

	public function push(mixed $value): void {
		$this->fields[] = $value;
	}

	public function set(array $array): void {
		$this->fields = $array;
	}

	public function setSelected(array $array, array $fieldNames): void {
		foreach ($fieldNames as $field_name) {
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
