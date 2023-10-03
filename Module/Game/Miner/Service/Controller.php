<?php
namespace Module\Game\Miner\Service;

use Module\ModuleController;
use Zodream\Disk\File;

class Controller extends ModuleController {

    protected File|string $layout = 'main';

    public function rules() {
        return [
            '*' => '@'
        ];
    }
}