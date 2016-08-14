<?php
namespace Domain\Model\Question;

use Domain\Model\Model;
/**
* Class QuestionModel
* @property integer $id
* @property string $title
* @property string $content
* @property integer $user_id
* @property integer $status
* @property integer $count
* @property integer $update_at
* @property integer $create_at
*/
class QuestionModel extends Model {
	public static $table = 'question';

	protected function rules() {
		return array (
		  'title' => 'required|string:3-200',
		  'content' => '',
		  'user_id' => 'int',
		  'status' => 'int',
		  'count' => 'int',
		  'update_at' => 'int',
		  'create_at' => 'int',
		);
	}

	protected function labels() {
		return array (
		  'id' => 'Id',
		  'title' => 'Title',
		  'content' => 'Content',
		  'user_id' => 'User Id',
		  'status' => 'Status',
		  'count' => 'Count',
		  'update_at' => 'Update At',
		  'create_at' => 'Create At',
		);
	}
}