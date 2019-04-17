<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
use Zodream\Html\Dark\Form;
/** @var $this View */
$this->title = '编辑菜单';
?>

<div class="page-tip">
    <p class="blue">操作提示</p>
    <ul>
        <li>编辑菜单</li>
    </ul>
    <span class="toggle"></span>
</div>

<?=Form::open($model, './admin/menu/save')?>
    <?=Form::text('name', true)?>
    <?=Form::select('parent_id', [$menu_list, ['顶级菜单']])?>
    <?php $this->extend('../layouts/editor', [
            'tab_id' => false
        ]); ?>
    <button type="submit" class="btn btn-success">确认保存</button>
    <a class="btn btn-danger" href="javascript:history.go(-1);">取消修改</a>
<?= Form::close('id') ?>
