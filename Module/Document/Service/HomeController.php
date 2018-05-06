<?php
namespace Module\Document\Service;

use Module\Document\Domain\Model\ProjectModel;
use Module\ModuleController;

class HomeController extends ModuleController {
    public function indexAction() {
        $project_list = ProjectModel::select('name', 'id')->all();
        return $this->show(compact('project_list'));
    }
}