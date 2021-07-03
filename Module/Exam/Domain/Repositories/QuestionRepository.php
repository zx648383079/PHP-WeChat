<?php
declare(strict_types=1);
namespace Module\Exam\Domain\Repositories;

use Domain\Model\SearchModel;
use Module\Exam\Domain\Model\QuestionAnalysisModel;
use Module\Exam\Domain\Model\QuestionAnswerModel;
use Module\Exam\Domain\Model\QuestionModel;
use Module\Exam\Domain\Model\QuestionOptionModel;

class QuestionRepository {
    public static function getList(string $keywords = '', int $course = 0, int $user = 0) {
        return QuestionModel::with('course', 'user')
            ->when($course > 0, function ($query) use ($course) {
                $query->where('course_id', $course);
            })
            ->when(!empty($keywords), function ($query) {
                SearchModel::searchWhere($query, ['title']);
            })->when($user > 0, function ($query) use ($user) {
                $query->where('user_id', $user);
            })->orderBy('id', 'desc')->page();
    }

    public static function searchList(string $keywords = '', int $course = 0, int $user = 0) {
        return QuestionModel::with('course', 'user')
            ->when($course > 0, function ($query) use ($course) {
                $query->where('course_id', $course);
            })
            ->when(!empty($keywords), function ($query) {
                SearchModel::searchWhere($query, ['title']);
            })->when($user > 0, function ($query) use ($user) {
                $query->where('user_id', $user);
            })->orderBy('id', 'desc')
            ->select('id', 'title', 'course_id', 'user_id', 'type', 'easiness', 'created_at')->page();
    }

    public static function selfList(string $keywords = '', int $course = 0) {
        return static::getList($keywords, $course, auth()->id());
    }

    public static function get(int $id) {
        return QuestionModel::findOrThrow($id, '数据有误');
    }

    public static function getFull(int $id, int $user = 0) {
        $model = static::get($id);
        if ($user > 0 && $model->user_id !== $user) {
            throw new \Exception('数据有误');
        }
        $data = $model->toArray();
        $data['option_items'] = $model->option_items;
        $data['analysis_items'] = $model->analysis_items;
        if ($model->material_id > 0) {
            $data['material'] = $model->material;
        }
        return $data;
    }

    public static function selfFull(int $id) {
        return static::getFull($id, auth()->id());
    }

    public static function save(array $data, int $user = 0) {
        $id = $data['id'] ?? 0;
        unset($data['id']);
        if ($id < 1 && static::checkRepeat($data)) {
            throw new \Exception('请不要重复添加');
        }
        $model = QuestionModel::findOrNew($id);
        if ($id > 0 && $user > 0 && $model->user_id !== $user) {
            throw new \Exception('无法保存');
        }
        $model->load($data);
        $model->user_id = auth()->id();
        if (!$model->save()) {
            throw new \Exception($model->getFirstError());
        }
        if (isset($data['option_items'])) {
            QuestionOptionModel::batchSave($model, $data['option_items']);
        }
        if (isset($data['analysis_items'])) {
            QuestionAnalysisModel::batchSave($model, $data['analysis_items']);
        }
        return $model;
    }

    public static function checkRepeat(array $data): bool {
        $userId = auth()->id();
        return QuestionModel::where('type', $data['type'] ?? 0)
            ->where('title', $data['title'])
            ->when(isset($data['course_id']) && $data['course_id'] > 0, function ($query) use ($data) {
                $query->where('course_id', $data['course_id']);
            })
            ->when(isset($data['content']), function ($query) use ($data) {
                $query->where('content', $data['content']);
            })
            ->when(isset($data['image']), function ($query) use ($data) {
                $query->where('image', $data['image']);
            })
            ->where('user_id', $userId)
            ->count() > 0;
    }

    public static function selfSave(array $data) {
        if (isset($data['material'])) {
            $m = MaterialRepository::save($data['material']);
            $data['material_id'] = $m->id;
        }
        return static::save($data, auth()->id());
    }

    public static function remove(int $id, int $user = 0) {
        $model = QuestionModel::where('id', $id)->when($user > 0, function ($query) use ($user) {
            $query->where('user_id', $user);
        })->first();
        if (empty($model)) {
            throw new \Exception('无权限删除');
        }
        $model->delete();
        QuestionAnswerModel::where('question_id', $id)->delete();
        QuestionOptionModel::where('question_id', $id)->delete();
        QuestionAnalysisModel::where('question_id', $id)->delete();
    }

    public static function selfRemove(int $id) {
        static::remove($id, auth()->id());
    }

    public static function search(string $keywords = '', int|array $id = 0) {
        return SearchModel::searchOption(
            QuestionModel::query(),
            ['title'],
            $keywords,
            $id === 0 ? [] : compact('id')
        );
    }

    public static function check(string $title, int $id = 0) {
        return QuestionModel::with('course', 'user')->where('title', $title)->where('id', '<>', $id)->orderBy('id', 'desc')->get();
    }

    public static function suggestion(string $keywords, int $course = 0) {
        return QuestionModel::with('course')
                ->when($course > 0, function ($query) use ($course) {
                    $query->where('course_id', $course);
                })
                ->when(!empty($keywords), function ($query) {
                    SearchModel::searchWhere($query, ['title']);
                })->orderBy('id', 'desc')->limit(5)->get('id', 'title', 'course_id', 'type', 'easiness');
    }
}