<?php
namespace Module\Document\Service;

use Module\Document\Domain\Model\ApiModel;
use Module\Document\Domain\Model\ProjectModel;

class ProjectController extends Controller {

    public function indexAction($id) {
        $project = ProjectModel::find($id);
        $tree_list = ApiModel::getTree($id);
        return $this->show(compact('project', 'tree_list'));
    }

}