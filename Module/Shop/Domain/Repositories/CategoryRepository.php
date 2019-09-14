<?php
namespace Module\Shop\Domain\Repositories;

use Module\Shop\Domain\Models\CategoryModel;

class CategoryRepository {

    public static function getHomeFloor() {
        $categories_tree = CategoryModel::cacheTree();
        $floor_categories = [];
        foreach ($categories_tree as $item) {
            $item['children'] = isset($item['children']) ? array_splice($item['children'], 0, 4) : [];
            if (!empty($item['children'])) {
                $item['children'] = array_values($item['children']);
            }
            $item['goods'] = GoodsRepository::getRecommendQuery('is_hot')
                ->whereIn('cat_id', CategoryModel::getChildrenWithParent($item['id']))
                ->limit(4)->all();
            $item['url'] = url('./category', ['id' => $item['id']]);
            $floor_categories[] = $item;
        }
        return $floor_categories;
    }
}