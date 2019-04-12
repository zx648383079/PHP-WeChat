<?php
namespace Module\CMS\Domain\Fields;

use Module\CMS\Domain\Model\ContentModel;
use Module\CMS\Domain\Model\ModelFieldModel;
use Zodream\Database\Schema\Column;
use Zodream\Html\Dark\Theme;

class File extends BaseField {

    public function options(ModelFieldModel $field) {
        return implode('', [
            Theme::text('setting[option][allow]', '*', '允许格式'),
            Theme::text('setting[option][length]', '2M', '允许大小'),
        ]);
    }



    public function converterField(Column $column, ModelFieldModel $field) {
        // TODO: Implement converterField() method.
    }

    public function toInput($value, ModelFieldModel $field) {
        return Theme::file($field->field, $value, $field->name, null,
            $field->is_required > 0);
    }
}