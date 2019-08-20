<?php
namespace Module\CMS\Domain\Scene;

use Module\CMS\Domain\Fields\BaseField;
use Module\CMS\Domain\FuncHelper;
use Module\CMS\Domain\Model\ModelFieldModel;
use Module\CMS\Domain\Model\ModelModel;
use Zodream\Database\Query\Builder;
use Zodream\Database\Schema\Column;
use Zodream\Helpers\Str;
use Zodream\Infrastructure\Traits\ErrorTrait;

abstract class BaseScene implements SceneInterface {

    use ErrorTrait;

    protected $site = 1;

    /**
     * @var ModelModel
     */
    protected $model;

    public function setModel(ModelModel $model, $site = 1) {
        $this->model = $model;
        $this->site = $site;
        return $this;
    }

    /**
     * @return ModelFieldModel[]
     */
    public function fieldList() {
        return FuncHelper::fieldList($this->model->id);
    }

    protected function getGroupFieldName() {
        $field_list = $this->fieldList();
        $main = ['id', 'cat_id', 'model_id', 'user_id', 'status', 'view_count', 'updated_at', 'created_at'];
        $extra = [];
        foreach ($field_list as $item) {
            if ($item->is_main > 0) {
                $main[] = $item->field;
                continue;
            }
            $extra[] = $item->field;
        }
        return [$main, $extra];
    }

    protected function splitByField(array $params) {
        list($mainNames, $extraNames) = $this->getGroupFieldName();
        $main = [];
        $extra = [];
        foreach ($params as $key => $item) {
            if (in_array($key, $mainNames)) {
                $main[$key] = $item;
                continue;
            }
            if (in_array($key, $extraNames)) {
                $extra[$key] = $item;
                continue;
            }
        }
        return [$main, $extra];
    }

    /**
     * 添加
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    protected function addWhere(Builder $query, $params = []) {
        if (empty($params)) {
            return $query;
        }
        list($main, $extra) = $this->splitByField($params);
        if (!empty($extra)) {
            $main['id'] =
                $this->addWhereOrIn($this->extendQuery(), $extra)->pluck('id');
        }
        return $this->addWhereOrIn($query, $main);
    }

    private function addWhereOrIn(Builder $query, array $params) {
        if (empty($params)) {
            return $query;
        }
        foreach ($params as $key => $item) {
            if (is_array($item)) {
                $query->whereIn($key, $item);
                continue;
            }
            $query->where($key, $item);
        }
        return $query;
    }

    protected function getResultByField(Builder $query, $field = '*') {

    }

    public function toInput(ModelFieldModel $field, array $data) {
        if ($field->is_disable > 0) {
            return null;
        }
        return self::newField($field->type)->toInput(isset($data[$field->field])
            ? $data[$field->field] : '', $field);
    }

    /**
     * @param array $data
     * @return array [main, extend]
     * @throws \Exception
     */
    public function filterInput(array $data) {
        $field_list = $this->fieldList();
        if (empty($field_list)) {
            return [[], []];
        }
        $extend = $main = [];
        foreach ($field_list as $field) {
            $value = static::newField($field->type)->filterInput(isset($data[$field->field]) ? $data[$field->field]
                : null, $field);
            if ($field->is_main > 0) {
                $main[$field->field] = $value;
                continue;
            }
            $extend[$field->field] = $value;
        }
        return [$main, $extend];
    }

    /**
     * @param $type
     * @return BaseField
     * @throws \Exception
     */
    public static function newField($type) {
        $maps = [
            'switch' => 'SwitchBox',
        ];
        if (isset($maps[$type])) {
            $type = $maps[$type];
        }
        $class = 'Module\CMS\Domain\Fields\\'.Str::studly($type);
        if (class_exists($class)) {
            return new $class;
        }
        throw new \Exception(
            __('Field "{type}" not exist!', compact('type'))
        );
    }

    public static function converterTableField(Column $column, ModelFieldModel $field) {
        static::newField($field->type)->converterField($column, $field);
    }
}