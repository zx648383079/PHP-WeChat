<?php
namespace Module\Shop\Domain\Entities;

use Domain\Entities\Entity;

class CouponEntity extends Entity {

    const TYPE_MONEY = 0;  // 优惠
    const TYPE_DISCOUNT = 1; // 折扣

    const RULE_NONE = 0;
    const RULE_GOODS = 1;
    const RULE_CATEGORY = 2;
    const RULE_BRAND = 3;
    const RULE_STORE = 4;

    const SEND_RECEIVE = 0; // 前台领取
    const SEND_GOODS = 1;   // 购买商品
    const SEND_ORDER = 2;   // 订单金额
    const SEND_SIGN = 3;    // 签到
    const SEND_USER = 4;    // 按用户


    public static function tableName() {
        return 'shop_coupon';
    }

    protected function rules() {
        return [
            'name' => 'required|string:0,30',
            'thumb' => 'string:0,255',
            'type' => 'int:0,99',
            'rule' => 'int:0,99',
            'rule_value' => 'int:0,99',
            'min_money' => '',
            'money' => '',
            'send_type' => 'int',
            'send_value' => 'int',
            'every_amount' => 'int',
            'start_at' => 'int',
            'end_at' => 'int',
            'created_at' => 'int',
            'updated_at' => 'int',
        ];
    }

    protected function labels() {
        return [
            'id' => 'Id',
            'name' => '优惠券名称',
            'thumb' => '优惠券图片',
            'type' => '优惠券类型',
            'rule' => '使用规则',
            'rule_value' => 'Rule Value',
            'min_money' => '使用门槛',
            'money' => '面值',
            'send_type' => '发放类型',
            'send_value' => 'Send Value',
            'every_amount' => '每人限领',
            'start_at' => '有效期',
            'end_at' => 'Created At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}