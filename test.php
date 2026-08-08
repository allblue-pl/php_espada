<?php
/**
 * @phpstan-type T_PageArgs array<string, mixed>
 */

class A {
    /** @var T_PageArgs */
    private array $a;

    public function __construct() {
        $this->a = [];
    }

    /**
     * @return T_PageArgs    
     */
    public function a(int $b): array {
        return $this->a;
    }
}