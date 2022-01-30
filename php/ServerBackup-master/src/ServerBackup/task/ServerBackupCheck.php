<?php

namespace ServerBackup\task;

use pocketmine\scheduler\Task;

use pocketmine\utils\TextFormat;

use ServerBackup\task\ServerBackupAsync_3;
use ServerBackup\task\ServerBackupAsync_4;

use ServerBackup\Loader;

class ServerBackupCheck extends Task{

    /** @var Loader */
    public $owner;

    public function __construct(Loader $owner){
        $this->owner = $owner;
    }

    public function onRun($currentTick): void{
        if (date('i') === '00' and $this->owner->backupMode === 'off'){

            if (substr($this->owner->getServer()->getApiVersion(), 0, 1) === '4'){ // API 4.0.0
                $this->owner->getServer()->getAsyncPool()->submitTask(new ServerBackupAsync_4($this->owner->getServer()->getDataPath()));
            }else{ // API 3.0.0, 2.0.0, ...
                $this->owner->getServer()->getAsyncPool()->submitTask(new ServerBackupAsync_3($this->owner->getServer()->getDataPath()));
            }
            
            $this->owner->getServer()->getLogger()->notice($this->owner->prefix . '서버 자동 백업을 시작 했습니다..');
            $this->owner->getServer()->getLogger()->notice($this->owner->prefix . '모드 상태: ' . TextFormat::YELLOW . $this->owner->mode);

        }
    }

}