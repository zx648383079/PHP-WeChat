<?php
namespace Module\Exam\Domain\Migrations;


use Module\Exam\Domain\Entities\CourseEntity;
use Module\Exam\Domain\Entities\CourseLinkEntity;
use Module\Exam\Domain\Entities\QuestionAnswerEntity;
use Module\Exam\Domain\Entities\QuestionEntity;
use Module\Exam\Domain\Entities\QuestionOptionEntity;
use Module\Exam\Domain\Entities\QuestionWrongEntity;
use Zodream\Database\Migrations\Migration;
use Zodream\Database\Schema\Table;

class CreateExamTables extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $this->append(CourseEntity::tableName(), function (Table $table) {
            $table->setComment('科目');
            $table->set('id')->pk(true);
            $table->set('name')->varchar(30)->notNull();
            $table->set('thumb')->varchar(200)->defaultVal('');
            $table->set('description')->varchar(200)->defaultVal('');
            $table->set('parent_id')->int(11, true)->defaultVal(0);
            $table->timestamps();
        })->append(CourseLinkEntity::tableName(), function (Table $table) {
            $table->setComment('科目关联表');
            $table->set('course_id')->int(11, true, true);
            $table->set('link_id')->int(11, true, true);
            $table->set('title')->varchar(100)->defaultVal('');
        })->append(QuestionEntity::tableName(), function (Table $table) {
            $table->setComment('题库');
            $table->set('id')->pk(true);
            $table->set('title')->varchar(255)->notNull();
            $table->set('image')->varchar(200)->defaultVal('');
            $table->set('course_id')->int(11, true, true);
            $table->set('parent_id')->int(11, true)->defaultVal(0);
            $table->set('type')->tinyint(1)->defaultVal(0)->comment('题目类型');
            $table->set('easiness')->tinyint(1)->defaultVal(0)->comment('难易程度');
            $table->timestamps();
        })->append(QuestionOptionEntity::tableName(), function (Table $table) {
            $table->setComment('题选择选项');
            $table->set('id')->pk(true);
            $table->set('content')->varchar(255)->notNull();
            $table->set('question_id')->int(11, true, true);
            $table->set('type')->tinyint(1)->defaultVal(0);
            $table->set('is_right')->bool()->defaultVal(0)->comment('是否是正确答案');
        })->append(QuestionAnswerEntity::tableName(), function (Table $table) {
            $table->setComment('题选择选项');
            $table->set('id')->pk(true);
            $table->set('question_id')->int(11, true, true);
            $table->set('content')->text()->comment('题目答案');
            $table->set('analysis')->text()->comment('题库解析');
        })->append(QuestionWrongEntity::tableName(), function (Table $table) {
            $table->setComment('错题集');
            $table->set('id')->pk(true);
            $table->set('question_id')->int(11, true, true);
            $table->set('user_id')->int(11, true, true);
            $table->set('frequency')->smallInt(5)
                ->defaultVal(1)->comment('出错次数');
            $table->timestamps();
        });
        parent::up();
    }
}