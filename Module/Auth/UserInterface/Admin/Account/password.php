<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
/** @var $this View */

$this->title = '更改密码';

?>

    <h1><?=$this->title?></h1>
    <form data-type="ajax" action="<?=$this->url('./account/update_password')?>" method="post" class="form-table" role="form">
        <div class="input-group">
            <label>原密码</label>
            <div>
                <input name="old_password" type="password" class="form-control"  placeholder="输入原密码" required>
            </div>
        </div>
        <div class="input-group">
            <label>新密码</label>
            <div>
                <input name="password" type="password" class="form-control"  placeholder="输入新密码" required>                
            </div>
        </div>
        <div class="input-group">
            <label>确认密码</label>
            <div>
                <input name="confirm_password" type="password" class="form-control"  placeholder="确认密码" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success">确认更改</button>
        <a class="btn btn-danger" href="javascript:history.go(-1);">取消修改</a>
    </form>