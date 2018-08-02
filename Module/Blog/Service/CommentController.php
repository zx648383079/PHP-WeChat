<?php
namespace Module\Blog\Service;

use Module\Blog\Domain\Model\BlogModel;
use Module\Blog\Domain\Model\CommentModel;
use Module\ModuleController;
use Zodream\Domain\Access\Auth;
use Zodream\Infrastructure\Http\Request;

class CommentController extends ModuleController {

    protected function rules() {
        return [
            'index' => '*',
            'save' => '*',
            'more' => '*',
            '*' => '@',
        ];
    }

    public function indexAction($blog_id) {
        $hot_comments = CommentModel::where([
            'blog_id' => intval($blog_id),
            'parent_id' => 0,
        ])->where('agree', '>', 0)->order('agree desc')->limit(4)->all();
        return $this->show(compact('hot_comments', 'blog_id'));
    }

    public function moreAction($blog_id, $parent_id = 0, $sort = 'created_at', $order = 'desc') {
        $comment_list = CommentModel::with('replies')
            ->where([
            'blog_id' => intval($blog_id),
            'parent_id' => intval($parent_id)
        ])->order($sort, $order)->page();
        if ($parent_id > 0) {
            return $this->show('rely', compact('comment_list', 'parent_id'));
        }
        return $this->show(compact('comment_list'));
    }

    public function saveAction() {
        $data = app('request')->get('name,email,url,content,parent_id,blog_id');
        if (!BlogModel::canComment($data['blog_id'])) {
            return $this->jsonFailure('不允许评论！');
        }
        if (!auth()->guest()) {
            $data['user_id'] = auth()->id();
            $data['name'] = auth()->user()->name;
        }
        $data['parent_id'] = intval($data['parent_id']);

        $last = CommentModel::where('blog_id', $data['blog_id'])->where('parent_id', $data['parent_id'])->order('position desc')->one();
        $data['position'] = empty($last) ? 1 : ($last->position + 1);
        $model = CommentModel::create($data);
        if (empty($model)) {
            return $this->jsonFailure('评论失败！');
        }
        BlogModel::record()->where('id', $data['blog_id'])->updateOne('comment_count');
        return $this->jsonSuccess($model);
    }

    public function disagreeAction($id) {
        $id = intval($id);
        if (!CommentModel::canAgree($id)) {
            return $this->jsonFailure('一个用户只能操作一次！');
        }
        $model = CommentModel::find($id);
        $model->disagree ++;
        $model->save();
        return $this->jsonSuccess($model->disagree);
    }

    public function agreeAction($id) {
        $id = intval($id);
        if (!CommentModel::canAgree($id)) {
            return $this->jsonFailure('一个用户只能操作一次！');
        }
        $model = CommentModel::find($id);
        $model->agree ++;
        $model->save();
        return $this->jsonSuccess($model->agree);
    }

    public function reportAction($id) {

    }

    public function logAction() {
        CommentModel::alias('c');
    }
}