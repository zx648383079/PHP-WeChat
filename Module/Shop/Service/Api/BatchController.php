<?php
declare(strict_types=1);
namespace Module\Shop\Service\Api;

use Module\Shop\Domain\Repositories\AdRepository;
use Module\Shop\Domain\Repositories\ArticleRepository;
use Module\Shop\Domain\Repositories\BrandRepository;
use Module\Shop\Domain\Repositories\CategoryRepository;
use Module\Shop\Domain\Repositories\GoodsRepository;
use Module\Shop\Domain\Repositories\SearchRepository;
use Module\Shop\Module;
use Zodream\Route\Controller\Concerns\BatchAction;

class BatchController extends Controller {

    use BatchAction;

    public function indexAction() {
        return $this->render($this->invokeBatch([
            'category' => sprintf('%s::%s', CategoryRepository::class, 'getList'),
            'brand' => sprintf('%s::%s', BrandRepository::class, 'recommend'),
            'cart' => sprintf('%s::%s', Module::class, 'cart'),
            'hot_keywords' => sprintf('%s::%s', SearchRepository::class, 'hotKeywords'),
            'notice' => sprintf('%s::%s', ArticleRepository::class, 'getNotices'),
            'help' => sprintf('%s::%s', ArticleRepository::class, 'getHelps'),
            'home_product' => sprintf('%s::%s', GoodsRepository::class, 'homeRecommend'),
            'banner' => sprintf('%s::%s', AdRepository::class, 'banners'),
        ]));
    }

}