<?php
namespace Module\Shop\Service\Mobile;

use Module\Shop\Domain\Model\AttributeModel;
use Module\Shop\Domain\Model\CommentModel;
use Module\Shop\Domain\Model\GoodsModel;

class GoodsController extends Controller {

    public function indexAction($id) {
        $goods = GoodsModel::find($id);
        $goods_list = GoodsModel::where('is_best', 1)->limit(3)->all();
        $comment_list = CommentModel::with('images', 'user')->where('item_type', 0)
            ->where('item_id', $id)->limit(3)->all();
        return $this->show(compact('goods', 'goods_list', 'comment_list'));
    }

    public function priceAction($id, $amount = 1, $properties = null) {
        $goods = GoodsModel::find($id);
        $price = $goods->final_price($amount, $properties);
        $box = AttributeModel::getProductAndPriceWithProperties($properties, $id);
        return $this->jsonSuccess([
            'price' => $price,
            'total' => $price * $amount,
            'stock' => !empty($box['product']) ? $box['product']->stock : $goods->stock
        ]);
    }

    public function commentAction($id) {
        /** @var Page $goods_list */
        $comment_list = CommentModel::with('images', 'user')->where('item_type', 0)
            ->where('item_id', $id)->page();
        if (app('request')->isAjax()) {
            return $this->jsonSuccess([
                'html' => $this->renderHtml('page', compact('comment_list', 'id')),
                'has_more' => $goods_list->hasMore()
            ]);
        }
        return $this->show(compact('comment_list', 'id'));
    }
}