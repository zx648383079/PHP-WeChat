<?php
declare(strict_types=1);
namespace Module\CMS\Domain\Fields;

use Module\CMS\Domain\Model\ModelFieldModel;
use Zodream\Database\Contracts\Column;
use Zodream\Html\Dark\Theme;

class Select extends BaseField {

    public function options(ModelFieldModel $field, bool $isJson = false): array|string {
        if ($isJson) {
            return [
                [
                    'name' => 'items',
                    'label' => '选项',
                    'type' => 'textarea',
                ],
            ];
        }
        return implode('', [
            Theme::textarea('setting[option][items]', '', '选项'),
        ]);
    }



    public function converterField(Column $column, ModelFieldModel $field): void {
        $column->string()->default('')->comment($field->name);
    }

    public function toInput($value, ModelFieldModel|array $field, bool $isJson = false): array|string {
        $options = static::fieldSetting($field, 'option', 'items');
        if ($isJson) {
            return [
                'name' => $field['field'],
                'label' => $field['name'],
                'type' => 'date',
                'value' => $value,
                'items' => self::textToItems($options)
            ];
        }
        return (string)Theme::select($field['field'], self::textToItems($options), $value, $field['name']);
    }

    public function toText(mixed $value, ModelFieldModel|array $field): string {
        $items = self::textToItems(static::fieldSetting($field, 'option', 'items'));
        return array_key_exists($value, $items) ? (string)$items[$value] : '';
    }
}