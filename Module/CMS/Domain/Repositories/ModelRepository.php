<?php
declare(strict_types=1);
namespace Module\CMS\Domain\Repositories;

use Domain\Model\SearchModel;
use Module\Auth\Domain\Events\ManageAction;
use Module\CMS\Domain\Model\ModelFieldModel;
use Module\CMS\Domain\Model\ModelModel;
use Module\CMS\Domain\Scene\SingleScene;

class ModelRepository {
    public static function getList(string $keywords = '', int $type = 0) {
        return ModelModel::query()
            ->when(!empty($keywords), function ($query) {
                SearchModel::searchWhere($query, ['title']);
            })->page();
    }

    public static function get(int $id) {
        return ModelModel::findOrThrow($id, '数据有误');
    }

    public static function save(array $data) {
        $id = $data['id'] ?? 0;
        unset($data['id']);
        $model = ModelModel::findOrNew($id);
        if ($id > 0) {
            unset($data['table']);
        } elseif (ModelModel::where('`table`', $data['table'])->count() > 0) {
            throw new \Exception('表名已存在');
        }
        $model->load($data);
        if (!$model->save()) {
            throw new \Exception($model->getFirstError());
        }
        event(new ManageAction('cms_model_edit', '', 32, $id));
        if ($id < 1) {
            CMSRepository::scene()->setModel($model)->initModel();
        }
        return $model;
    }

    public static function remove(int $id) {
        $model = ModelModel::where('id', $id);
        if (!$model) {
            throw new \Exception('模型不存在');
        }
        $model->delete();
        ModelFieldModel::where('model_id', $id)->delete();
        CMSRepository::removeModel($model);
    }

    public static function all(int $type = 0) {
        return ModelModel::query()
            ->where('type', $type)
            ->get('id', 'name');
    }

    public static function fieldList(int $model) {
        return ModelFieldModel::query()
            ->where('model_id', $model)->get();
    }

    public static function field(int $id) {
        return ModelFieldModel::findOrThrow($id, '数据有误');
    }

    public static function fieldSave(array $data) {
        $id = $data['id'] ?? 0;
        unset($data['id']);
        $model = ModelFieldModel::findOrNew($id);
        if ($model->is_system > 0) {
            $model->name = $data['name'];
            $model->save();
            return $model;
        }
        if (ModelFieldModel::where('`field`', $model->field)
                ->where('id', '<>', $id)
                ->where('model_id', $model->model_id)
                ->count() > 0) {
            throw new \Exception('字段已存在');
        }
        $old = $id > 0 ? $model->get() : [];
        $model->load($data);
        $scene = CMSRepository::scene();
        if (!$model->save()) {
            throw new \Exception($model->getFirstError());
        }
        if ($id > 0) {
            $model->setOldAttribute($old);
            $scene->setModel($model->model)->updateField($model);
        } else {
            $scene->setModel($model->model)->addField($model);
        }
        return $model;
    }

    public static function fieldRemove(int $id) {
        $model = ModelFieldModel::find($id);
        if (!$model) {
            throw new \Exception('字段不存在');
        }
        if ($model->is_system > 0) {
            throw new \Exception('系统自带字段禁止删除');
        }
        CMSRepository::scene()->setModel($model->model)->removeField($model);
        $model->delete();
        return $model;
    }

    public static function fieldToggle(int $id, array $data) {
        $model = ModelFieldModel::find($id);
        $maps = ['is_disable'];
        foreach ($data as $action => $val) {
            if (is_int($action)) {
                if (empty($val)) {
                    continue;
                }
                list($action, $val) = [$val, $model->{$val} > 0 ? 0 : 1];
            }
            if (empty($action) || !in_array($action, $maps)) {
                continue;
            }
            $model->{$action} = intval($val);
        }
        $model->save();
        return $model;
    }

    /**
     * 对属性进行分组
     * @param int $model
     * @return array
     */
    public static function fieldGroupByTab(int $model) {
        $tab_list = self::fieldTab($model);
        $data = [];
        foreach ($tab_list as $i => $item) {
            $data[$item] = [
                'name' => $item,
                'active' => $i < 1,
                'items' => []
            ];
        }
        $field_list = ModelFieldModel::where('model_id', $model)->orderBy([
            'position' => 'asc',
            'id' => 'asc'
        ])->get();
        foreach ($field_list as $item) {
            $name = $item->tab_name;
            if (empty($name) || !in_array($name, $tab_list)) {
                $name = $item->is_main > 0 ? $tab_list[0] : $tab_list[1];
            }
            $data[$name]['items'][] = $item;
        }
        return array_values($data);
    }

    /**
     * 获取所有的分组标签
     * @param int $model
     * @return string[]
     */
    public static function fieldTab(int $model) {
        $tab_list = ModelFieldModel::where('model_id', $model)->pluck('tab_name');
        $data = ['基本', '高级'];
        foreach ($tab_list as $item) {
            $item = trim($item);
            if (empty($item) || in_array($item, $data)) {
                continue;
            }
            $data[] = $item;
        }
        return $data;
    }

    public static function fieldType(): array {
        $items = (new ModelFieldModel())->type_list;
        $data = [];
        foreach ($items as $value => $name) {
            $data[] = compact('name', 'value');
        }
        return $data;
    }

    public static function fieldOption(string $type, int $field) {
        $model = ModelFieldModel::findOrNew($field);
        $field = SingleScene::newField($type);
        $data = $field->options($model, true);
        return empty($data) || !is_array($data) ? [] : $data;
    }
}