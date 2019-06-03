<?php
namespace Service\Admin;
/**
 * 后台首页
 */
use Infrastructure\SiteMap;
use Module\Auth\Domain\Model\VisitLogModel;
use Module\Blog\Domain\Model\BlogModel;
use Zodream\Route\Router;


class HomeController extends Controller {

	function indexAction() {
        $user = auth()->user();
        //$search = VisitLogModel::getTopSearch();
        return $this->show(array(
//            'name' => $user['name'],
//            'num' => $user['login_num'],
//            'ip' => $user['previous_ip'],
//            'date' => $user['previous_at'],
//            'search' => $search
        ));
	}

	public function sitemapAction() {
	    url()->useCustomScript();
	    $map = new SiteMap();
        $map->add(url('/'), time());
        $map->add(url('/blog'), time());
        $map->add(url('/cms'), time());
        $map->add(url('/doc'), time());
        $modules = config()->moduleConfigs('Home')['modules'];
	    app(Router::class)->module('blog', function () use ($map) {
            $blog_list = BlogModel::orderBy('id', 'desc')->get('id', 'updated_at');
            foreach ($blog_list as $item) {
                $map->add($item->url,
                    $item->updated_at, SiteMap::CHANGE_FREQUENCY_WEEKLY, .8);
            }
        }, $modules);
        $map->toXml();
        url()->useCustomScript(false);
        return $this->show(compact('map'));
    }

    public function cacheAction() {
	    cache()->delete();
	    return $this->show();
    }
}