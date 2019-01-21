<?php
namespace Module\Shop\Service\Mobile;

use Module\Shop\Domain\Model\OrderAddressModel;
use Module\Shop\Domain\Model\OrderGoodsModel;
use Module\Shop\Domain\Model\OrderModel;


class OrderController extends Controller {

    protected function rules() {
        return [
            '*' => '@'
        ];
    }

    public function indexAction() {
        $order_list = OrderModel::with('goods')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->page();
        return $this->show(compact('order_list'));
    }

    public function detailAction($id) {
        $order = OrderModel::find($id);
        $goods_list = OrderGoodsModel::where('order_id', $id)->all();
        $address = OrderAddressModel::where('order_id', $id)->one();
        return $this->show(compact('order', 'goods_list', 'address'));
    }

    public function payAction($id) {
        $order = OrderModel::find($id);
        $order->pay();
    }
}