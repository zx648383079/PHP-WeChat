<?php
use Zodream\Template\View;
/** @var $this View */
$this->extend('layouts/header');
?>
    <section class="container">
        <div class="login-box">
            <form class="form-ico login-form" action="<?= $this->url('./password') ?>" method="POST">
                <div class="input-group">
                    <input type="email" placeholder="请输入邮箱" required>
                    <i class="fa fa-at" aria-hidden="true"></i>
                </div>
                <div class="input-group">
                    <input type="password" placeholder="请输入密码" required>
                    <i class="fa fa-lock" aria-hidden="true"></i>
                </div>
                <div class="input-group">
                    <input type="password" placeholder="请确认密码" required>
                    <i class="fa fa-circle-o" aria-hidden="true"></i>
                </div>

                <button type="submit" class="btn btn-full">确定修改</button>
            </form>
            
        </div>
    </section>
<?php
$this->extend('layouts/footer');
?>