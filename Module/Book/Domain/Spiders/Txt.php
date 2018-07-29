<?php
namespace Module\Book\Domain\Spiders;

use Module\Book\Domain\Model\BookChapterModel;
use Zodream\Disk\Stream;
use Zodream\Domain\Debug\Log;

class Txt {

    public function isTitle(string $line): bool {
        //return preg_match('/^第.{1,20}[章|节|卷].{1,50}$/', trim($line));
        return preg_match('/^(\d|\d{2}|1\d{0,2})$/', trim($line));
    }

    public function save(string $title, $content) {
        if (is_array($content)) {
            $content = implode(PHP_EOL, $content);
        }
        Log::info($title);
        $model =  new BookChapterModel([
            'title' => trim($title),
            'content' => $content,
            'book_id' => 15
        ]);
        $model->save();
    }

    public function invoke($file) {
        $stream = new Stream($file);
        $title = '';
        $lines = [];
        $i = 0;
        $stream->openRead();
        while (!$stream->isEnd()) {
            $line = $stream->readLine();
            if (!$this->isTitle($line)) {
                $lines[] = $line;
                continue;
            }
            $i ++;
            $this->save($title, $lines);
            $title = $line;
            $lines = [];
        }
        if (!empty($title)) {
            $i ++;
            $this->save($title, $lines);
        }Log::notice(sprintf('成功导入%s章', $i));
        $stream->close();
    }
}