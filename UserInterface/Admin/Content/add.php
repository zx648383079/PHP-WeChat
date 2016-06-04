<?php
defined('APP_DIR') or exit();
use Zodream\Domain\Html\Bootstrap\PanelWidget;
use Zodream\Domain\Html\Bootstrap\FormWidget;
/** @var $this \Zodream\Domain\Response\View */
$this->extend(array(
    'layout' => array(
        'head'
    ))
);

echo PanelWidget::show(array(
    'head' => '添加模型',
    'body' => FormWidget::begin($this->get('data'))
        ->hidden('id')
        ->select('type', array(
            '内容模型',
            '表单模型'
        ), array(
            'label' => '模型类型：',
        ))
        ->text('name', array('label' => '名称：'))
        ->text('tablename', array('label' => '数据表名：'))
        ->text('categorytpl', array('label' => '栏目模板：'))
        ->text('listtpl', array('label' => '列表模板：'))
        ->text('showtpl', array('label' => '列表模板：'))
        ->button()->end()
))
?>




<?php
$this->extend(array(
        'layout' => array(
            'foot'
        ))
);
?>