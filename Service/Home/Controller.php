<?php
namespace Service\Home;

use Zodream\Route\Controller\Controller as BaseController;

abstract class Controller extends BaseController {

    public $layout = 'main';

    public function prepare() {
    }
}