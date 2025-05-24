<?php
use ScssPhp\ScssPhp\Compiler;

class SCSS {
    private $compiler;

    public function __construct() {
        $this->compiler = new Compiler();
    }

    public function compile($scss) {
        try {
            return $this->compiler->compileString($scss)->getCss();
        } catch (Exception $e) {
            error_log('SCSS Compilation Error: ' . $e->getMessage());
            return '';
        }
    }
}
