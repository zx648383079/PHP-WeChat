<?php
namespace Module\Forum\Domain\Model;

use Domain\Model\Model;
/**
* Class ForumModel
* @property integer $id
* @property integer $parent
* @property string $type
* @property string $name
* @property integer $status
* @property integer $position
* @property integer $threads
* @property integer $todaypost
* @property integer $posts
*/
class ForumModel extends Model {
	public static function tableName() {
        return 'forum';
    }


    protected function rules() {
		return array (
		  'parent' => 'int',
		  'type' => '',
		  'name' => 'required|string:3-50',
		  'status' => 'int:0-1',
		  'position' => 'int',
		  'threads' => 'int',
		  'todaypost' => 'int',
		  'posts' => 'int',
		);
	}

	protected function labels() {
		return array (
		  'id' => 'Id',
		  'parent' => 'Parent',
		  'type' => 'Type',
		  'name' => 'Name',
		  'status' => 'Status',
		  'position' => 'Position',
		  'threads' => 'Threads',
		  'todaypost' => 'Todaypost',
		  'posts' => 'Posts',
		);
	}
}