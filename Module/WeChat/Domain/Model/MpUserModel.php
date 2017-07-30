<?php
namespace Module\WeChat\Domain\Model;

use Domain\Model\Model;


/**
 * 微信公众号用户资料表
 * 从公众号中拉取的数据可以保存在此表
 * @package callmez\wechat\models
 */
class MpUserModel extends Model {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'wechat_mp_user';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'required'],
            [['id', 'sex', 'subscribe_time', 'group_id'], 'integer'],
            [['nickname'], 'string', 'max' => 20],
            [['city', 'country', 'province', 'language'], 'string', 'max' => 40],
            [['avatar', 'remark'], 'string', 'max' => 255],
            [['union_id'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function labels() {
        return [
            'id' => '粉丝ID',
            'nickname' => '昵称',
            'sex' => '性别',
            'city' => '所在城市',
            'country' => '所在省',
            'province' => '微信ID',
            'language' => '用户语言',
            'avatar' => '用户头像',
            'subscribe_time' => '关注时间',
            'union_id' => '用户头像',
            'remark' => '备注',
            'group_id' => '分组ID',
            'updated_at' => '修改时间',
        ];
    }
}