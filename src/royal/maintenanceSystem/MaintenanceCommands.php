<?php

namespace royal\maintenanceSystem;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use royal\maintenanceSystem\task\MaintenanceTask;

class MaintenanceCommands extends Command
{
    public function __construct (string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        $this->setPermission("maintenance.use");
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute (CommandSender $sender, string $commandLabel, array $args): bool
    {
        if ($sender instanceof Player) {
            if ($this->testPermission($sender, "maintenance.use")) {
                $sender->sendMessage("tu n'a pas la permission");
                return true;
            }
            $form = new SimpleForm(function (Player $player, int $meta = null) {
                if ($meta === null) {
                    return true;
                }
                switch ($meta) {
                    case 0:
                        $this->sendOnMaintenance($player);
                        break;
                    case 1:
                        $dir = Main::getInstance()->getServer()->getDataPath() . "server.properties";
                        $test = new Config($dir);
                        $test->set("white-list", false);
                        $test->save();
                        break;
                }
                return true;
            });
            $form->addButton("Maintenance on");
            $form->addButton("Maintenance off");
            $sender->sendForm($form);
        }
        return true;
    }

    public function sendOnMaintenance (Player $sender)
    {
        $form = new CustomForm(function (Player $player, array $meta = null) {
            var_dump($meta);
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new MaintenanceTask($meta[1],$meta[2], $meta[0]), 20);
        });
        $form->setTitle("Maintenance System");
        $form->addInput(Main::$config->get("form-reason"));
        $form->addToggle(Main::$lang->get("form-toggle-shutdown"));
        $form->addSlider("time", 1, 500);
        $sender->sendForm($form);
    }
}