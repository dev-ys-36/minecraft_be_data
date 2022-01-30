<?php

namespace ServerBackup\task;

use pocketmine\scheduler\AsyncTask;

use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;

use pocketmine\Server;

class ServerBackupAsync_4 extends AsyncTask{

    /** @var string */
    public $path;

    /** @var string */
    public $backupPath;

    /** @var string */
    public $date;

    /** @var string */
    public $serverOS;

    /** @var int */
    public $file_size;

    /** @var array */
    public $file_list;

    /** @var int */
    public $startTime;

    /** @var int */
    public $endTime;

    /** @var string */
    public $mode;

    public function __construct(string $dataPath){
        $this->path = $dataPath;
        $this->date = date('Y-m-d-H-i-s');
        $this->serverOS = Utils::getOS();

        $plugin = Server::getInstance()->getPluginManager()->getPlugin('ServerBackup');
        $this->mode = $plugin->mode;
    }

    public function onRun(): void{
        $this->startTime = time();
        $this->OSCheck();
    }

    public function onCompletion(Server $server): void{
        $this->endTime = time();
        $time = $this->endTime - $this->startTime;

        $plugin = Server::getInstance()->getPluginManager()->getPlugin('ServerBackup');
        Server::getInstance()->broadcastMessage($plugin->prefix . '정상적으로 서버 백업이 완료 되었습니다..');
        Server::getInstance()->broadcastMessage($plugin->prefix . '진행된 시간: ' . TextFormat::YELLOW . $time . TextFormat::WHITE . '초');
        //Server::getInstance()->broadcastMessage($plugin->prefix . '백업 파일 목록: ' . TextFormat::YELLOW . implode(', ', $this->file_list));
        Server::getInstance()->broadcastMessage($plugin->prefix . '백업 파일의 크기: ' . TextFormat::YELLOW . $plugin->filesize_unit($this->file_size));
        Server::getInstance()->broadcastMessage($plugin->prefix . '백업 파일이 저장된 경로: ' . TextFormat::YELLOW . $this->backupPath);

        $plugin->backupMode = 'off';
        $plugin->mode = 'off';
    }

    public function OSCheck(): void{
        if ($this->serverOS === 'win'){
            $this->WindowsBackup();
        }else if ($this->serverOS === 'linux'){
            $this->LinuxBackup();
        }else{
            // nothing..
        }
    }

    public function LinuxBackup(): void{
        $path = str_replace(explode('/', $this->path)[mb_substr_count($this->path, '/', 'utf-8') - 1] . '/', '', $this->path);
        if (!is_dir($path . 'backup/')){
            @mkdir($path . 'backup/');
        }
        $zip = new \ZipArchive;
        $zip->open($path . 'backup/' . $this->date . '.zip', \ZipArchive::CREATE);
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $objects){
            if (!$objects->isDir()){
                if ($this->mode === 'on'){
                    $zip->addFile($objects, substr($objects, strlen($this->path)));
                }else if ($this->mode === 'off'){
                    if (strpos($objects->getPathname(), $this->path . 'bin/') === false
                        and strpos($objects->getPathname(), $this->path . 'src/') === false // still in use..
                        and strpos($objects->getPathname(), $this->path . 'vendor/') === false // still in use..
                        and strpos($objects->getPathname(), $this->path . 'crashdumps/') === false
                        and strpos($objects->getPathname(), $this->path . 'PocketMine-MP.phar') === false){
                        $zip->addFile($objects, substr($objects, strlen($this->path)));
                    }
                }
            }
        }
        $zip->close();
        $this->backupPath = $path . 'backup/' . $this->date . '.zip';
        $this->file_size = filesize($path . 'backup/' . $this->date . '.zip');
        //$this->file_list = (array)scandir($path . 'backup/' . $this->date . '.zip'); // because return object(Volatile)
    }

    public function WindowsBackup(): void{
        $path = str_replace(explode('\\', $this->path)[mb_substr_count($this->path, '\\', 'utf-8') - 1] . '\\', '', $this->path);
        if (!is_dir($path . 'backup\\')){
            @mkdir($path . 'backup\\');
        }
        $zip = new \ZipArchive;
        $zip->open($path . 'backup\\' . $this->date . '.zip', \ZipArchive::CREATE);
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $objects){
            if (!$objects->isDir()){
                if ($this->mode === 'on'){
                    $zip->addFile($objects, substr($objects, strlen($this->path)));
                }else if ($this->mode === 'off'){
                    $zip->addFile($objects, substr($objects, strlen($this->path)));
                    if (strpos($objects->getPathname(), $this->path . 'bin\\') === false
                        and strpos($objects->getPathname(), $this->path . 'src\\') === false // still in use..
                        and strpos($objects->getPathname(), $this->path . 'vendor\\') === false // still in use..
                        and strpos($objects->getPathname(), $this->path . 'crashdumps\\') === false
                        and strpos($objects->getPathname(), $this->path . 'PocketMine-MP.phar') === false){
                        $zip->addFile($objects, substr($objects, strlen($this->path)));
                    }
                }
            }
        }
        $zip->close();
        $this->backupPath = $path . 'backup\\' . $this->date . '.zip';
        $this->file_size = filesize($path . 'backup\\' . $this->date . '.zip');
        //$this->file_list = (array)scandir($path . 'backup\\' . $this->date . '.zip'); // because return object(Volatile)
    }

}
