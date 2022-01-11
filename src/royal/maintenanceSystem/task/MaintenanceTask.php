<?php
namespace royal\maintenanceSystem\task;

use JsonException;
use pocketmine\player\Player;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use royal\maintenanceSystem\Main;
use royal\maintenanceSystem\MaintenanceCommands;

class MaintenanceTask extends Task{
    public static int $timer;
    public static int $timerstart;
    public string $text;
    public function __construct (int $time = 100, string $text = null)
    {
        self::$timer = $time;
        self::$timerstart = $time;
        $this->text = $text;
    }

    /**
     * @throws JsonException
     */
    public function onRun (): void
    {
        if (self::$timer === self::$timerstart){
            foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player){
                if ($player instanceof Player){
                    $message = str_replace(["{time}"], [self::$timer], Main::$config->get("maintenance-announce"));
                    $player->sendMessage($message);
                }
            }
        }
        $conf = Main::$config->get("secound-announce");
        foreach ($conf as $times){
            if (self::$timer === $times){
                foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player){
                    if ($player instanceof Player){
                        if ($this->text === null){
                            $message = str_replace(["{time}"], [self::$timer], Main::$config->get("maintenance-announce"));
                            $player->sendMessage($message);
                        }else{
                            $message = str_replace(["{time}"], [self::$timer], $this->text);
                            $player->sendMessage($message);
                        }
                    }
                }
            }
        }
        if (self::$timer <= 1){
            $dir = Main::getInstance()->getServer()->getDataPath()."server.properties";
            $test = new Config($dir);
            $test->set("white-list", true);
            $test->save();
            if (Main::$config->get("tranfere-to-other-server") === true){
                $this->transferePlayers();
            }
            Main::getInstance()->getServer()->getConfigGroup()->setConfigBool("white-list", true);
            Main::getInstance()->getServer()->shutdown();
        }else{
            --self::$timer;
        }
    }
    public function transferePlayers(){
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player){
            if ($player instanceof Player){
                $player->transfer(Main::$config->get("ip-server"), Main::$config->get("port-server"), Main::$config->get("message-to-kick"));
            }
        }
    }
}