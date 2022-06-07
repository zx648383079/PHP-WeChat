<?php
declare(strict_types=1);
namespace Module\ResourceStore\Domain\Repositories;

use Module\ResourceStore\Domain\Models\ResourceFileModel;
use Module\ResourceStore\Domain\Models\ResourceModel;
use Zodream\Disk\Directory;
use Zodream\Disk\File;
use Zodream\Disk\ZipStream;
use Zodream\Infrastructure\Contracts\Http\Input;

final class UploadRepository {

    protected static function folder(): Directory {
        return app_path()->directory('data/demo');
    }

    public static function saveFile(Input $input) {
        return ResourceRepository::storage()->addFile($input->file('file'));
    }

    public static function file(ResourceFileModel $model) {
        return ResourceRepository::storage()->getFile($model->file);
    }

    public static function resourceFolder($id): Directory {
        if ($id instanceof ResourceModel) {
            $id = $id->id;
        } elseif ($id instanceof ResourceFileModel) {
            $id = $id->res_id;
        }
        return self::folder()->directory($id.'');
    }

    public static function unzipFile(ResourceFileModel $model) {
        $folder = self::resourceFolder($model);
        if ($folder->exist()) {
            $folder->delete();
        }
        $folder->create();
        $file = self::file($model);
        $zip = new ZipStream($file);
        $zip->extractTo($folder);
    }

    public static function fileMap(ResourceModel $model) {
        $folder = self::resourceFolder($model);
        return self::getFiles($folder);
    }

    public static function getFiles(Directory $folder) {
        $items = [];
        if (!$folder->exist()) {
            return $items;
        }
        $folder->map(function ($file) use (&$items) {
            if ($file instanceof File) {
                $items[] = [
                    'name' => $file->getName(),
                    'type' => 1,
                    'icon' => 'fa-file'
                ];
                return;
            }
            $items[] = [
                'name' => $file->getName(),
                'type' => 0,
                'icon' => 'fa-folder',
                'children' => self::getFiles($file)
            ];
        });
        return $items;
    }
}