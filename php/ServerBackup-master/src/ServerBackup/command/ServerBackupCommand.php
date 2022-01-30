<?php

namespace ServerBackup\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

use ServerBackup\task\ServerBackupAsync_3;
use ServerBackup\task\ServerBackupAsync_4;

use ServerBackup\Loader;

class ServerBackupCommand extends Command{

    /** @var Loader */
    public $owner;

    public function __construct(Loader $owner){
        $this->owner = $owner;
        parent::__construct('sb', 'ServerBackupCommand', '/sb');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender->isOp()){
            $sender->sendMessage($this->owner->prefix . '명령어를 실행 할 권한이 없습니다..');
            return true;
        }

        if ($sender instanceof Player){
            $sender->sendMessage($this->owner->prefix . '게임내에서 명령어를 실행 할 수 없습니다..');
            return true;
        }

        if ($this->owner->backupMode === 'on'){
            $sender->sendMessage($this->owner->prefix . '백업이 진행중 입니다.. 잠시만 기다려주세요..');
            return true;
        }

        $this->owner->mode = 'off';
        $this->owner->backupMode = 'on';

        if (isset($args[0]) and $args[0] === 'all'){
            $this->owner->mode = 'on';
        }

        if (substr($this->owner->getServer()->getApiVersion(), 0, 1) === '4'){ // API 4.0.0
            $this->owner->getServer()->getAsyncPool()->submitTask(new ServerBackupAsync_4($this->owner->getServer()->getDataPath()));
        }else{ // API 3.0.0, 2.0.0, ...
            $this->owner->getServer()->getAsyncPool()->submitTask(new ServerBackupAsync_3($this->owner->getServer()->getDataPath()));
        }

        $this->owner->getServer()->getLogger()->notice($this->owner->prefix . '서버 수동 백업을 시작 했습니다..');
        $this->owner->getServer()->getLogger()->notice($this->owner->prefix . '모드 상태: ' . TextFormat::YELLOW . $this->owner->mode);
        return true;
    }

}
