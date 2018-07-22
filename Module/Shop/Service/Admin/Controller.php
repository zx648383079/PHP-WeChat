<?php
namespace Module\Shop\Service\Admin;

use Module\ModuleController;
use Zodream\Infrastructure\Http\URL;

class Controller extends ModuleController {

    public $layout = '/Admin/layouts/main';

    protected function rules() {
        return [
            '*' => '@'
        ];
    }

    protected function getUrl($path, $args = []) {
        return (string)URL::to('./admin/'.$path, $args);
    }

    public function redirectWithMessage($url, $message, $time = 4, $status = 404) {
        return $this->show('/admin/prompt', compact('url', 'message', 'time'));
    }
}