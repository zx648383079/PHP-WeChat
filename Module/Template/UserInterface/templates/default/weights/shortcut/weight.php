<?php

use Module\Template\Domain\Model\SiteWeightModel;
use Module\Template\Domain\VisualEditor\BaseWeight;

class ShortcutWeight extends BaseWeight {

    /**
     * 获取生成的部件视图
     * @param SiteWeightModel $model
     * @return mixed
     */
    public function render(SiteWeightModel $model): string {
        $content = $model->content;
        $key_items = [];
        foreach (explode('\n', $content) as $line) {
            $args = explode(' ', $line, 2);
            $key_items[] = [
                'name' => $args[0],
                'items' =>  $this->splitKeys($args[1])
            ];
        }
        return $this->show('view', compact('key_items'));
    }

    public function renderForm(SiteWeightModel $model): string {
        $content = $model->content;
        $title = $model->title;
        return $this->show('config', compact('content', 'title'));
    }

    private function splitKeys(string $line): array {
        $items = [];
        $pos = 0;
        while (true) {
            $i = strpos($line, '/', $pos);
            $j = strpos($line, '+', $pos);
            if (!$i) {
                $items[] = [
                    'key' => substr($line, $pos),
                ];
                break;
            }
            $min = $i;
            $sep = '/';
            if ($j > 0 && $j < $i) {
                $min = $j;
                $sep = '+';
            }
            $items[] = [
                'key' => substr($line, $pos, $min - $pos),
            ];
            $items[] = [
                'sep' => $sep,
            ];
            $pos = $min + 1;
        }
        return $items;
    }
}