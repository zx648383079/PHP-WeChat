<?php
/** @var $this \Zodream\Template\View */
$this->registerJsFile('@jquery.min.js')
    ->registerJsFile('@jquery.dialog.min.js')
    ->registerJsFile('@jquery.datetimer.min.js')
    ->registerJsFile('@main.min.js')
    ->registerJsFile('@finance.min.js');
?>

    </div>
</div>
<?=$this->footer()?>
</body>
</html>
