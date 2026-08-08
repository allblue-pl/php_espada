<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


interface ILayout {
    public function addL(string $holderName, Layout $layout): Layout;
}
