<?php
namespace Module\Shop\Domain;

use Module\Shop\Domain\Model\CartModel;
use Module\Shop\Domain\Model\GoodsModel;
use Traversable;
use Zodream\Helpers\Json;
use IteratorAggregate;
use Zodream\Infrastructure\Interfaces\JsonAble;
use Zodream\Infrastructure\Interfaces\ArrayAble;
use ArrayIterator;

class ShoppingCart implements IteratorAggregate, JsonAble, ArrayAble {
    /**
     * @var CartModel[]
     */
    protected $data = [];

    /**
     * @var array[] [goodsId => [price => CartModel]]
     */
    protected $goodsMap = [];

    public function __construct() {
        $this->loadFromDb();
    }

    protected function loadFromDb() {
        $this->setGoods(CartModel::with('goods')
            //->where('user_id', auth()->id())
            ->all());
    }

    /**
     * @param CartModel[] $goods
     * @return $this
     */
    public function setGoods(array $goods) {
        $this->data = [];
        foreach ($goods as $item) {
            $this->addCart($item);
        }
        return $this;
    }

    public function addCart(CartModel $cart) {
        if ($this->hasCart($cart->id)) {
            $this->data[$cart->id]->number += $cart->number;
            return $this;
        }
        $this->data[$cart->id] = $cart;
        if (!array_key_exists($cart->goods_id, $this->goodsMap)) {
            $this->goodsMap[$cart->goods_id] = [];
        }
        $this->goodsMap[$cart->goods_id][$cart->price] = $cart;
        return $this;
    }

    /**
     *
     * @param integer $goodsId
     * @param float $price
     * @return CartModel[]|bool|CartModel
     */
    public function getCartByGoodsId($goodsId, $price = null) {
        if (!$this->hasGoodsId($goodsId, $price)) {
            return false;
        }
        if (is_null($price)) {
            return $this->goodsMap[$goodsId];
        }
        return $this->goodsMap[$goodsId][$price];
    }

    public function getCartByGoods(GoodsModel $goods) {
        return $this->getCartByGoodsId($goods->id, $goods->price);
    }

    public function hasGoods(GoodsModel $goods) {
        return $this->hasGoodsId($goods->id, $goods->price);
    }

    public function hasGoodsId($goodsId, $price = null) {
        if (!array_key_exists($goodsId, $this->goodsMap)) {
            return false;
        }
        return is_null($price) || array_key_exists($price, $this->goodsMap[$price]);
    }

    public function getCart($cartId) {
        if ($this->hasCart($cartId)) {
            return $this->data[$cartId];
        }
        return false;
    }

    public function hasCart($cartId) {
        return array_key_exists($cartId, $this->data);
    }

    public function addGoods(GoodsModel $goods, $number = 1) {
        if ($number < 1) {
            return $this;
        }
        if ($this->hasGoods($goods)) {
            $this->getCartByGoods($goods)->number += $number;
            return $this;
        }
        $cart = $this->goodsToCart($goods);
        $cart->number = $number;
        return $this->addCart($cart);
    }

    protected function goodsToCart(GoodsModel $goods) {
        $model = new CartModel();
        $model->goods_id = $goods->id;
        $model->price = $goods->price;
        $model->user_id = auth()->id();
        return $model;
    }

    public function removeGoods(GoodsModel $goods) {
        return $this->removeGoodsId($goods->id, $goods->price);
    }

    public function removeGoodsId($goodsId, $price = null) {
        if (!$this->hasGoodsId($goodsId, $price)) {
            return $this;
        }
        if (!is_null($price)) {
            return $this->removeCart($this->goodsMap[$goodsId][$price]);
        }
        foreach ($this->goodsMap[$goodsId] as $item) {
            $this->removeCart($item);
        }
        unset($this->goodsMap[$goodsId]);
        return $this;
    }

    public function removeCart(CartModel $cart) {
        if (!$this->hasCart($cart->id)) {
            return $this;
        }
        unset($this->data[$cart->id]);
        unset($this->goodsMap[$cart->goods_id][$cart->price]);
        return $this;
    }

    public function removeCartId($cartId) {
        if (!$this->hasCart($cartId)) {
            return $this;
        }
        return $this->removeCart($this->data[$cartId]);
    }

    public function removeAll() {
        foreach ($this->data as $cart) {
            $cart->delete();
        }
        $this->data = $this->goodsMap = [];
        return $this;
    }

    /**
     * @return CartModel[]
     */
    public function getCarts() {
        return $this->data;
    }

    public function getNumber() {
        $count = 0;
        foreach ($this->getCarts() as $item) {
            $count += $item->number;
        }
        return $count;
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->getCarts() as $item) {
            $total += $item->total;
        }
        return $total;
    }


    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() {
        return new ArrayIterator($this->getCarts());
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return $this->getCarts();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0) {
        return Json::encode($this->getCarts());
    }
}