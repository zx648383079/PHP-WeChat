<?php
namespace Module\Shop\Service;

use Module\Shop\Domain\Model\AddressModel;
use Module\Shop\Domain\Model\CartModel;
use Module\Shop\Domain\Model\OrderGoodsModel;
use Module\Shop\Domain\Model\OrderModel;
use Module\Shop\Domain\Model\PaymentModel;
use Module\Shop\Domain\Model\ShippingModel;

/**
 * 收银员
 * @package Module\Shop\Service\Mobile
 */
class CashierController extends Controller {

    public function indexAction() {
        $goods_list = $this->getGoodsList();
        $address = AddressModel::where('user_id', auth()->id())->one();
//        $order = OrderModel::preview($goods_list);
//        $shipping_list = ShippingModel::getByAddress($address);
//        $payment_list = PaymentModel::all();
        return $this->sendWithShare()->show(compact('goods_list', 'address', 'order', 'shipping_list', 'payment_list'));
    }


    public function checkoutAction() {
        $goods_list = $this->getGoodsList();
        $order = OrderModel::preview($goods_list);
        $order->createOrder();
        return $this->redirect($this->getUrl('cashier/pay', ['id' => $order->id]));
    }

    public function payAction($id) {
        $order = OrderModel::find($id);
        return $this->show(compact('order'));
    }


    /**
     * @return CartModel[]
     */
    protected function getGoodsList() {
        return CartModel::getAllGoods();
    }
}