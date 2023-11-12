<?php

use Module\Template\Domain\Model\SitePageModel;
use Module\Template\Domain\Model\SitePageWeightModel;
use Module\Template\Domain\Model\SiteWeightModel;
use Module\Template\Domain\VisualEditor\BaseWeight;
use Zodream\Helpers\Json;
use Module\Template\Domain\VisualEditor\VisualHelper;
use Module\Template\Domain\VisualEditor\VisualInput;

class NavbarWeight extends BaseWeight {

    public function render(SiteWeightModel $model): string {
        $data = VisualHelper::formatFormData($model->content, $this->defaultData());
        $data['title'] = $model->title;
        $data['nav_items'] = VisualHelper::formatUrlTree($data['nav_items']);
        return $this->show('view', $data);
    }

    public function renderForm(SiteWeightModel $model): array {
        $data = VisualHelper::formatFormData($model->content, $this->defaultData());
//        $weightId = SitePageWeightModel::where('page_id', $this->pageId())
//            ->where('weight_id', '<>', $model->id)->pluck('weight_id');
//        $data['weight_list'] = empty($weightId) ? [] : SiteWeightModel::whereIn('id', $weightId)->get('id', 'title');
//        $data['page_list'] = SitePageModel::where('site_id', $model->site_id)
//            ->where('id', '<>', $this->pageId())->get('id', 'title');
        return [
            VisualInput::title($model->title),
            VisualInput::tree('nav_items', '网址', $data['nav_items'], VisualInput::url()),
            VisualInput::color('un_selected_color', '未选中颜色', $data['un_selected_color']),
            VisualInput::color('selected_color', '选中颜色', $data['selected_color'])
        ];
    }

    public function validateForm(array $input): array {
        $data = [];
        foreach($this->defaultData() as $key => $def) {
            $value = $input[$key]??$def;
            $data[$key] = is_array($def) && is_string($value) ? Json::decode($value) : $value;
        }
        return [
            'title' => $input['title']??'',
            'content' => Json::encode($data),
        ];
    }

    private function formatData($content) {
        $items = empty($content) ? [] : Json::decode($content);
        $data = [];
        foreach($this->defaultData() as $key => $value) {
            if (!isset($items[$key])) {
                $data[$key] = $value;
                continue;
            }
            if (is_int($value)) {
                $data[$key] = intval($items[$key]);
            } else {
                $data[$key] = $items[$key];
            }
        }
        return $data;
    }
    
    private function defaultData(): array {
        return [
            'un_selected_color' => '',
            'selected_color' => '',
            'nav_items' => [],
        ];
    }
}