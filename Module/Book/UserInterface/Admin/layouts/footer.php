<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
/** @var $this View */
$this->registerJsFile('@jquery.min.js')
    ->registerJsFile('@main.min.js')
    ->registerJsFile('@jquery.dialog.min.js')
    ->registerJsFile('@book_admin.min.js');
?>
    </div>
</div>
<?=$this->footer()?>
</body>
</html>