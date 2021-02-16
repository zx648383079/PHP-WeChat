<?php
declare(strict_types=1);
namespace Module\Legwork\Domain\Repositories;

use Module\Legwork\Domain\Model\CategoryModel;
use Module\Legwork\Domain\Model\CategoryProviderModel;

class CategoryRepository {
    public static function getList(string $keywords = '') {
        return CategoryModel::query()->when(!empty($keywords), function ($query) {
            CategoryModel::searchWhere($query, ['name']);
        })->page();
    }

    public static function get(int $id) {
        return CategoryModel::findOrThrow($id, '数据有误');
    }

    public static function save(array $data) {
        $id = isset($data['id']) ? $data['id'] : 0;
        unset($data['id']);
        $model = CategoryModel::findOrNew($id);
        $model->load($data);
        if (!$model->save()) {
            throw new \Exception($model->getFirstError());
        }
        return $model;
    }

    public static function remove(int $id) {
        CategoryModel::where('id', $id)->delete();
    }

    public static function providerList(string $keywords = '', int $status = 0) {
        $catId = CategoryProviderModel::where('user_id', auth()->id())
            ->when($status > 0, function ($query) {
                $query->where('status', 1);
            })->pluck('cat_id');
        return CategoryModel::query()->when(!empty($keywords), function ($query) {
            CategoryModel::searchWhere($query, ['name']);
        })->whereIn('id', $catId)->page();
    }

    public static function all() {
        return CategoryModel::query()->get();
    }
}