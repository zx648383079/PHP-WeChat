<?php
namespace Module\WeChat\Domain\Model;

use Domain\Model\Model;

/**
 * @property integer $id
 * @property string $name
 * @property integer $wid
 * @property string $tag_id
 */
class UserGroupModel extends Model {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'wechat_user_group';
    }

    protected function rules() {
        return [
            'name' => 'required|string:0,20',
            'wid' => 'required|int',
            'tag_id' => 'string:0,20',
        ];
    }

    protected function labels() {
        return [
            'id' => 'Id',
            'name' => 'Name',
            'wid' => 'Wid',
            'tag_id' => 'Tag Id',
        ];
    }

}