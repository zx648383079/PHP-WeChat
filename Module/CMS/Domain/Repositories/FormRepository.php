<?php
declare(strict_types=1);
namespace Module\CMS\Domain\Repositories;

use Exception;
use Module\CMS\Domain\FuncHelper;
use Module\CMS\Domain\Model\ModelModel;
use Zodream\Infrastructure\Contracts\Http\Input;

class FormRepository {

    /**
     * @return ModelModel
     * @throws \Exception
     */
    public static function getModel(Input $input) {
        if ($input->has('id')) {
            return FuncHelper::model(intval($input->get('id')));
        }
        return FuncHelper::model($input->get('model'));
    }

    public static function formInputList(Input $input): array {
        $model = static::getModel($input);
        $scene = CMSRepository::scene()->setModel($model);
        $field_list = $scene->fieldList();
        $inputItems = [];
        foreach ($field_list as $item) {
            $inputItems[] = $scene->toInput($item, [], true);
        }
        return $inputItems;
    }

    public static function save(Input $input) {
        $model = static::getModel($input);
        if (empty($model) || $model->type < 1) {
            throw new Exception('表单数据错误');
        }
        $scene = CMSRepository::scene()->setModel($model);
        $id = 0;
        if ($model->setting('is_only')) {
            if (auth()->guest()) {
                throw new Exception('请先登录！');
            }
            $id = $scene->query()
                ->where('model_id', $model->id)
                ->where('user_id', auth()->id())
                ->value('id');
        }
        $data = $input->get();
        if ($id > 0) {
            $res = $scene->update($id, $data);
        } else {
            $res = $scene->insert($data);
        }
        if (!$res) {
            throw new Exception($scene->getFirstError());
        }
        return $res;
    }

}