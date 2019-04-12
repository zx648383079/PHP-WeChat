<?php
namespace Module\Shop\Domain\Models;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/15
 * Time: 19:07
 */
use Domain\Model\Model;

/**
 * Class PaymentModel
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $icon
 * @property string $description
 * @property string $settings
 */
class PaymentModel extends Model {

    public static function tableName() {
        return 'shop_payment';
    }

    protected function rules() {
        return [
            'name' => 'required|string:0,30',
            'code' => 'required|string:0,30',
            'icon' => 'string:0,100',
            'description' => 'string:0,255',
            'settings' => '',
        ];
    }

    protected function labels() {
        return [
            'id' => 'Id',
            'name' => '名称',
            'code' => 'Code',
            'icon' => '图标',
            'description' => '介绍',
            'settings' => '配置',
        ];
    }

    /**
     * @return float
     */
    public function getFee() {
        return 0;
    }

    public function pay(OrderModel $order) {
        $log = new PayLogModel();
        /** 生成支付请求并记录 */
    }

    public function callback() {

    }

    public static function paymentList() {
        return [
            'balance' =>  '余额支付',
            'alipay' =>  '支付宝支付',
            'wx' =>  '微信支付',
        ];
    }
}