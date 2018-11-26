<?php
namespace Module\Shop\Service\Admin;

use Module\Shop\Domain\Model\RegionModel;

class RegionController extends Controller {

    public function indexAction() {

    }

    public function treeAction() {
        return $this->jsonSuccess(RegionModel::cacheTree());
    }
}