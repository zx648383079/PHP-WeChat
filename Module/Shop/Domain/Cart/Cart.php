<?php
namespace Module\Shop\Domain\Cart;

use Module\Shop\Domain\Model\CartModel;
use Module\Shop\Domain\Model\GoodsModel;
use Traversable;
use Zodream\Helpers\Json;
use IteratorAggregate;
use Zodream\Infrastructure\Cookie;
use Zodream\Infrastructure\Interfaces\JsonAble;
use Zodream\Infrastructure\Interfaces\ArrayAble;
use ArrayIterator;

class Cart implements IteratorAggregate, JsonAble, ArrayAble {

    const COOKIE_KEY = 'cart_identifier';
    /**
     * @var Group[]
     */
    protected $groups = [];

    protected $booted = false;

    public function __construct() {
        $this->loadFromDb();
    }

    public function id() {
        $id = app('request')->cookie(self::COOKIE_KEY);
        if (!empty($id)) {
            return $id;
        }
        $id = md5(uniqid(null, true));
        Cookie::set(self::COOKIE_KEY, $id, 0, '/');
        return $id;
    }

    protected function loadFromDb() {
        $this->booted = false;
        $this->setGoods(CartModel::with('goods')
            ->where('user_id', auth()->id())
            ->all());
        $this->booted = true;
    }

    /**
     * @param CartModel[] $goods
     * @return $this
     */
    public function setGoods(array $goods) {
        $this->groups = [];
        foreach ($goods as $item) {
            $this->add($item);
        }
        return $this;
    }

    public function add(ICartItem $item) {
        foreach ($this->groups as $group) {
            if ($group->can($item)) {
                $group->add($item);
                return $this;
            }
        }
        $this->groups[] = new Group($item);
        return $this;
    }

    public function update($id, $amount) {
        $this->get($id)->updateAmount($amount);
        return $this;
    }

    public function get($id) {
        foreach ($this->groups as $group) {
            if ($item = $group->get($id)) {
                return $item;
            }
        }
        return null;
    }

    public function remove($id) {
        foreach ($this->groups as $group) {
            if ($this->groups->remove($id)) {
                return true;
            }
        }
        return false;
    }

    public function total() {
        $total = 0;
        foreach ($this->groups as $group) {
            $total += $group->total();
        }
        return $total;
    }

    public function all() {
        return $this->groups;
    }

    public function getIterator() {
        return new ArrayIterator($this->all());
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return array_map(function (Group $group) {
            return $group->toArray();
        }, $this->all());
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0) {
        return Json::encode($this->all());
    }
}