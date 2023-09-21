<?php
namespace Module\CMS\Domain\Model;

use Domain\Model\Model;
use Module\CMS\Domain\Entities\SiteEntity;
use Zodream\Helpers\Json;
use Zodream\Http\Uri;

/**
 * Class SiteModel
 * @package Module\CMS\Domain\Model
 * @property integer $id
 * @property string $title
 * @property string $keywords
 * @property string $description
 * @property string $logo
 * @property string $theme
 * @property integer $match_type
 * @property string $match_rule
 * @property integer $is_default
 * @property integer $status
 * @property string $language
 * @property string $options
 * @property integer $created_at
 * @property integer $updated_at
 */
class SiteModel extends SiteEntity {

    const MATCH_TYPE_DOMAIN = 0;
    const MATCH_TYPE_PATH = 1;

    public function getLogoAttribute() {
        $cover = $this->getAttributeSource('logo');
        if (empty($cover)) {
            $cover = '/assets/images/favicon.png';
        }
        return url()->asset($cover);
    }

    public function getOptionsAttribute() {
        $option = $this->getAttributeSource('options');
        if (empty($option)) {
            return [];
        }
        return is_array($option) ? $option : Json::decode($option);
    }

    public function setOptionsAttribute($value) {
        if (empty($value)) {
            $value = [];
        }
        if (is_array($value)) {
            $value = Json::encode($value);
        }
        $this->__attributes['options'] = $value;
    }

    public function getPreviewUrlAttribute() {
        if ($this->match_type < 1) {
            $uri = new Uri(url('./'));
            return (string)$uri->setHost($this->match_rule);
        }
        return url(sprintf('/%s', $this->match_rule));
    }

    public function url(string $path, array $data = []) {
        $rule = $this->match_rule;
        if ($this->match_type < 1) {
            $uri = new Uri(url($path, $data));
            return empty($rule) ? $uri : $uri->setHost($this->match_rule);
        }
        if (str_starts_with($path, './')) {
            $path = substr($path, 2);
        }
        if (empty($rule)) {
            return url(sprintf('/%s', $path), $data);
        }
        return url(sprintf('/%s/%s', $this->match_rule, $path), $data);
    }

    public function saveOption(array $data) {
        $options = $this->options;
        $items = [];
        foreach ((array)$options as $item) {
            if (empty($item) || !is_array($item)) {
                continue;
            }
            $items[$item['code']] = $item;
        }
        foreach ($data as $item) {
            if (empty($item) || !is_array($item)) {
                continue;
            }
            if ($item['code'] === 'theme') {
                $this->theme = $item['value'];
            }
            if (isset($items[$item['code']])) {
                $items[$item['code']] = array_merge($items[$item['code']], $item);
                continue;
            }
            $items[$item['code']] = $item;
        }
        $this->options = array_values($items);
        $this->save();
    }
}