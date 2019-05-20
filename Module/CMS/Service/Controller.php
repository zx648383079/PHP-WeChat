<?php
namespace Module\CMS\Service;

use Module\CMS\Domain\FuncHelper;
use Module\CMS\Domain\Model\CategoryModel;
use Module\ModuleController;
use Zodream\Service\Factory;
use Zodream\Template\Engine\ParserCompiler;

class Controller extends ModuleController {

    public function prepare() {
        Factory::view()
            ->setDirectory(Factory::view()->getDirectory()->directory('default'))
            ->setEngine(FuncHelper::register(new ParserCompiler()))
            ->setConfigs([
                'suffix' => '.html'
            ]);
        $categories_tree = FuncHelper::channels(['tree' => true]);
        $this->send(compact('categories_tree'));
    }
}