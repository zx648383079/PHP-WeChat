<?php
namespace Module\Shop\Service\Api;

use Module\Shop\Domain\Models\RegionModel;

class RegionController extends Controller {

    public function indexAction($id = 0) {
        $data = RegionModel::where('parent_id', intval($id))->all();
        return $this->render(compact('data'));
    }

    public function treeAction() {
        return $this->render(['data' => RegionModel::cacheTree()]);
    }
}