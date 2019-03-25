<?php
namespace Module\Shop\Service\Api;

use Module\Shop\Domain\Model\ArticleCategoryModel;
use Module\Shop\Domain\Model\ArticleModel;

class ArticleController extends Controller {

    public function indexAction($id = 0, $category = 0) {
        if ($id > 0) {
            return $this->runMethodNotProcess('detail', compact('id'));
        }
        $model_list = ArticleModel::with('category')->where('cat_id', $category)->select(ArticleModel::THUMB_MODE)->page();
        return $this->renderPage($model_list);
    }

    public function detailAction($id) {
        $article = ArticleModel::find($id);
        return $this->render($article);
    }

    public function categoryAction($parent_id = 0) {
        $cat_list = ArticleCategoryModel::where('parent_id', $parent_id)->all();
        return $this->render($cat_list);
    }
}