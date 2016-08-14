<?php
namespace Domain\Model;
/**
* Class OptionModel
* @property string $name
* @property string $value
* @property string $autoload
*/
class OptionModel extends Model {
	public static $table = 'option';

	protected $primaryKey = array (
		'name',
	);

	protected function rules() {
		return array (
		  'name' => 'required|string:3-255',
		  'value' => '',
		  'autoload' => '|string:3-20',
		);
	}

	protected function labels() {
		return array (
		  'name' => 'Name',
		  'value' => 'Value',
		  'autoload' => 'Autoload',
		);
	}

	/**
	 * FIND ALL TO ASSOC ARRAY
	 * @param array|string $where
	 * @return array
	 */
	public static function findOption($where = array()) {
		$data = static::find()->where($where)->all();
		$result = [];
		foreach ($data as $item) {
			$result[$item['name']] = $item['value'];
		}
		return $result;
	}
}