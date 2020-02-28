<?php
namespace Module\Counter\Domain\Middleware;

use Module\Counter\Domain\Events\JumpOut;
use Module\Counter\Domain\Events\Visit;
use Module\Counter\Domain\Listeners\JumpOutListener;
use Module\Counter\Domain\Listeners\VisitListener;
use Zodream\Service\Middleware\MiddlewareInterface;
use Zodream\Template\View;

class CounterMiddleware implements MiddlewareInterface {

    public function handle($payload, callable $next) {
        $this->boot();
        return $next($payload);
    }

    private function boot() {
        if (app()->isDebug() || app('request')->isCli()) {
            return;
        }
        $event = event();
        $event->listen(Visit::class, VisitListener::class)
            ->listen(JumpOut::class, JumpOutListener::class);
        $uri = app('request')->uri()->getPath();
        if (strpos($uri, '/counter') === 0 || strpos($uri, '/to') === 0) {
            return;
        }
        $event->dispatch(Visit::createCurrent());
        $js = <<<JS
var _hmt = _hmt || [];
(function() {
    var hm = document.createElement("script");
    hm.src = '/assets/js/hm.min.js';
    var s = document.getElementsByTagName("script")[0]; 
    s.parentNode.insertBefore(hm, s);
})();
JS;
        view()->registerJs($js, View::HTML_HEAD);
    }
}