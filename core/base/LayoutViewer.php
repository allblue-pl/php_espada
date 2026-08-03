<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

class LayoutViewer {
    private Fields $fields;
    private Holders $holders;

    public function __construct(Fields $fields, Holders $holders) {
        $this->fields = $fields;
        $this->holders = $holders;
    }

    public function &field(string $fieldName): mixed {
        return $this->fields->get($fieldName);
    }

    public function holder(string $holderName): void {
        $this->holders->view($holderName);
    }
}