<?php
namespace Module\Shop\Service;


class CouponController extends Controller {

    protected function rules() {
        return [
            'index' => '*',
            '*' => '@'
        ];
    }

    public function indexAction() {
        return $this->sendWithShare()->show();
    }

    public function myAction() {
        return $this->sendWithShare()->show();
    }
}