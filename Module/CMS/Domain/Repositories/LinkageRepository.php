<?php
declare(strict_types=1);
namespace Module\CMS\Domain\Repositories;

use Domain\Model\ModelHelper;
use Domain\Model\SearchModel;
use Module\CMS\Domain\Model\LinkageDataModel;
use Module\CMS\Domain\Model\LinkageModel;
use Zodream\Html\Tree;

class LinkageRepository {
    public static function getList(string $keywords = '') {
        $items = LinkageModel::query()
            ->when(!empty($keywords), function ($query) {
                SearchModel::searchWhere($query, ['name']);
            })->page();
        if ($items->isEmpty()) {
            return $items;
        }
        return ModelHelper::bindCount($items, LinkageDataModel::query(),
            'id', 'linkage_id');
    }

    public static function get(int $id) {
        return LinkageModel::findOrThrow($id, '数据有误');
    }

    public static function save(array $data) {
        $id = $data['id'] ?? 0;
        unset($data['id']);
        $model = LinkageModel::findOrNew($id);
        if (LinkageModel::where('id', '<>', $id)->where('code', $model->code)->count() > 0) {
            throw new \Exception('别名已存在');
        }
        $model->load($data);
        if (!$model->save()) {
            throw new \Exception($model->getFirstError());
        }
        return $model;
    }

    public static function remove(int $id) {
        LinkageModel::where('id', $id)->delete();
        LinkageDataModel::where('linkage_id', $id)->delete();
    }


    public static function dataList(int $linkage, string $keywords = '', int $parent = 0) {
        return LinkageDataModel::query()
            ->where('linkage_id', $linkage)
            ->where('parent_id', $parent)
            ->when(!empty($keywords), function ($query) {
                SearchModel::searchWhere($query, ['name']);
            })->page();
    }

    public static function dataSave(array $data) {
        $id = $data['id'] ?? 0;
        unset($data['id']);
        $model = LinkageDataModel::findOrNew($id);
        if (LinkageDataModel::where('linkage_id', $model->linkage_id)
        ->where('parent_id', $model->parent_id)
        ->where('id', '<>', $id)->where('name', $model->name)->count() > 0) {
            throw new \Exception('名称已存在');
        }
        $model->load($data);
        $model->createFullName();
        if (!$model->save()) {
            throw new \Exception($model->getFirstError());
        }
        CacheRepository::onLinkageUpdated(intval($model->linkage_id));
        return $model;
    }

    public static function dataRemove(int $id) {
        LinkageDataModel::where('id', $id)->delete();
    }

    public static function dataTree(int $id): array {
        return (new Tree(CacheRepository::getLinkageCache($id)))->makeIdTree();
    }
}