<?php
declare(strict_types=1);
namespace Module\CMS\Domain\Repositories;

use Module\CMS\Domain\FuncHelper;
use Module\CMS\Domain\Migrations\CreateCmsTables;
use Module\CMS\Domain\Model\CategoryModel;
use Module\CMS\Domain\Model\CommentModel;
use Module\CMS\Domain\Model\ContentModel;
use Module\CMS\Domain\Model\ModelModel;
use Module\CMS\Domain\Model\SiteLogModel;
use Module\CMS\Domain\Model\SiteModel;
use Module\CMS\Domain\Scene\BaseScene;
use Module\CMS\Domain\Scene\SceneInterface;
use Module\CMS\Domain\ThemeManager;
use Module\SEO\Domain\Option;
use Zodream\Database\Schema\Table;
use Zodream\Disk\Directory;
use Zodream\Helpers\Json;
use Zodream\Helpers\PinYin;
use Zodream\Helpers\Str;
use Zodream\Http\Uri;
use Zodream\Infrastructure\Error\Exception;
use Zodream\Template\Engine\ParserCompiler;
use Zodream\Template\ViewFactory;

class CMSRepository {

    /**
     * @var SiteModel
     */
    private static mixed $cacheSite;

    /**
     * @var string
     */
    private static string $cacheTheme = '';

    private static ?Directory $viewFolder = null;

    public static function theme() {
        if (!empty(self::$cacheTheme)) {
            return self::$cacheTheme;
        }
        $preview = request()->get('preview');
        if (!empty($preview)) {
            return self::$cacheTheme = $preview;
        }
        if (empty(static::site()->theme)) {
            return self::$cacheTheme = 'default';
        }
        return self::$cacheTheme = static::site()->theme;
    }

    public static function registerLocate(Directory $folder, ?string $language) {
        if (empty($language)) {
            $language = 'zh-cn';
        }
        $file = $folder->file(sprintf('languages/%s.json', $language));
        if (!$file->exist()) {
            return;
        }
        FuncHelper::$translateItems = Json::decode($file->read());
    }

    /**
     * 获取主题的路径
     * @return Directory
     * @throws \Exception
     */
    protected static function themeRootFolder(): Directory {
        $path = config('view.cms_directory');
        if (empty($path)) {
            return static::$viewFolder;
        }
        return app_path()->directory($path);
    }

    public static function registerView(string|SiteModel $theme = '',
                                        ?ViewFactory $provider = null): ViewFactory {
        $language = '';
        if (empty($theme)) {
            $theme = static::theme();
            $language = static::site()->language;
        } elseif ($theme instanceof SiteModel) {
            $language = $theme->language;
            $theme = $theme->theme;
        }
        if (empty($provider)) {
            $provider = view();
        }
        if (empty(static::$viewFolder)) {
            static::$viewFolder = $provider->getDirectory();
        }
        $dir = static::themeRootFolder()
            ->directory($theme);
        if (!$dir->exist()) {
            throw new Exception('THEME IS ERROR!');
        }
        $provider->setDirectory($dir)
            ->setEngine(FuncHelper::register(new ParserCompiler()))
            ->setConfigs([
                'suffix' => '.html'
            ]);
        static::registerLocate($dir, $language);
        return $provider;
    }

    public static function viewTemporary(callable $cb) {
        return view()->temporary(function ($provider) use ($cb) {
            static::registerView('', $provider);
            return call_user_func($cb, $provider);
        });
    }

    /**
     * @return BaseScene
     * @throws \Exception
     */
    public static function scene() {
        return app(SceneInterface::class);
    }

    /**
     * @return SiteModel
     * @throws \Exception
     */
    public static function site(mixed $model = null) {
        if (!empty($model)) {
            static::$cacheSite = $model;
        }
        if (!empty(self::$cacheSite)){
            return self::$cacheSite;
        }
        $request = request();
        $site = static::matchSite($request->host(), ltrim($request->path(), '/'));
        if ($site < 1) {
            throw new \Exception('无任何站点');
        }
        return self::$cacheSite = SiteModel::findOrThrow($site);
    }

    protected static function matchSite(string $host, string $path): int {
        $items = CacheRepository::getSiteCache();
        if (empty($items)) {
            return 0;
        }
        $default = $items[0]['id'];
        foreach ($items as $item) {
            if ($item['is_default'] > 0) {
                $default = $item['id'];
            }
            if ($item['match_type'] == SiteModel::MATCH_TYPE_DOMAIN) {
                if ($item['match_rule'] === $host) {
                    return $item['id'];
                }
                continue;
            }
            if ($item['match_type'] == SiteModel::MATCH_TYPE_PATH) {
                if (str_starts_with($path, ltrim($item['match_rule'], '/'))) {
                    return $item['id'];
                }
            }
        }
        return $default;
    }

    public static function siteId() {
        return static::site()->id;
    }

    public static function generateSite(SiteModel $site) {
        self::$cacheSite = $site;
        CreateCmsTables::createTable(CategoryModel::tableName(), function (Table $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('title', 100);
            $table->uint('type', 1)->default(0);
            $table->uint('model_id')->default(0);
            $table->uint('parent_id')->default(0);
            $table->string('keywords')->default('');
            $table->string('description')->default('');
            $table->string('thumb', 100)->default('')->comment('缩略图');
            $table->string('image', 100)->default('')->comment('主图');
            $table->text('content')->nullable();
            $table->string('url', 100)->default('');
            $table->uint('position', 2)->default(99);
            $table->string('groups')->default('');
            $table->string('category_template', 20)->default('');
            $table->string('list_template', 20)->default('');
            $table->string('show_template', 20)->default('');
            $table->text('setting')->nullable();
            $table->timestamps();
        });
        CreateCmsTables::createTable(SiteLogModel::tableName(), function (Table $table) {
            $table->id();
            $table->uint('model_id');
            $table->uint('item_type', 1)->default(0);
            $table->uint('item_id');
            $table->uint('user_id');
            $table->uint('action');
            $table->timestamp('created_at');
        });
        static::scene()->boot();
        (new ThemeManager())->apply($site->theme);
    }

    public static function generateCategoryTable(CategoryModel $model) {
        if ($model->type > 0 || !$model->model) {
            return;
        }
        static::scene()->setModel($model->model)->initTable();
    }

    public static function removeSite(SiteModel $site) {
        $model_list = ModelModel::query()->get();
        $old = self::$cacheSite;
        self::$cacheSite = $site;
        CreateCmsTables::dropTable(SiteLogModel::tableName());
        CreateCmsTables::dropTable(CategoryModel::tableName());
        CreateCmsTables::dropTable(ContentModel::tableName());
        CreateCmsTables::dropTable(CommentModel::tableName());
        foreach ($model_list as $item) {
            CMSRepository::scene()->setModel($item)->removeTable();
        }
        self::$cacheSite = $old;
    }

    public static function removeModel(ModelModel $model) {
        $site_list = SiteModel::query()->pluck('id');
        foreach ($site_list as $item) {
            CMSRepository::scene()->setModel($model, $item)->removeTable();
        }
    }

    public static function resetSite(int $id = 0) {
        if ($id > 0) {
            session([
                'cms_site' => $id
            ]);
        }
        $id = session('cms_site');
        if ($id > 0) {
            static::$cacheSite = SiteModel::find($id);
            return;
        }
    }

    public static function generateTableName(string $name) {
        if (empty($name)) {
            return Str::randomByNumber(8);
        }
        $val = PinYin::encode($name, 'all');
        return empty($val) ? Str::randomByNumber(8) : str_replace(' ', '_', $val);
    }
}