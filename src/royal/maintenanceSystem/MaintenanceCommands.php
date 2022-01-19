<?php

namespace royal\maintenanceSystem;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use royal\maintenanceSystem\task\MaintenanceTask;

class MaintenanceCommands extends Command
{
    public function __construct (string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        $this->setPermission("maintenance.use");
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute (CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {

            if ($this->testPermission($sender) || Server::getInstance()->isOp($sender->getName())) {
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
                            if (Main::$config->get("sendToDiscord") === true) {
                                $this->sendToDiscordOFF();
                            }
                            break;
                    }
                    return true;
                });
                $form->addButton("Maintenance on");
                $form->addButton("Maintenance off");
                $sender->sendForm($form);
            } else {

            }
        }
        return;
    }

    public function sendOnMaintenance (Player $sender)
    {
        $form = new CustomForm(function (Player $player, array $meta = null) {
            if ($meta === null){
                return;
            }
            if (Main::$config->get("sendToDiscord") === true) {
                $this->sendToDiscord($meta[3], $meta[2]);
            }
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new MaintenanceTask($meta[1], $meta[2], $meta[0]), 20);
        });
        $form->setTitle("Maintenance System");
        $form->addInput(Main::$lang->get("form-reason"));
        $form->addToggle(Main::$lang->get("form-toggle-shutdown"));
        $form->addSlider("time", 1, 500);
        if (Main::$config->get("sendToDiscord") === true) {
            $form->addInput(Main::$lang->get("form-discord"));
        }
        $sender->sendForm($form);
    }

    public function sendToDiscord ($text, $time, $on = false)
    {
        if ($on === false){
            $webHook = Main::$config->get("webHook-Discord");
            $messagesend = new Message();
            $send = new Webhook($webHook);
            $embded = new Embed();
            $embded->setTitle(Main::$lang->get("title"));
            $embded->setDescription(str_replace(["{time}"], [$time], Main::$lang->get("maintenance-announce")));
            if (empty($text)){
                $message = str_replace(["{time}"], [$time], Main::$lang->get("date"));
            }else{
                $message = str_replace(["{time}"], [$time], $text);
            }
            $embded->setFooter($message);
            $messagesend->addEmbed($embded);
            $send->send($messagesend);
        }
    }

    private function sendToDiscordOFF ()
    {
        $webHook = Main::$config->get("webHook-Discord");
        $messagesend = new Message();
        $send = new Webhook($webHook);
        $embded = new Embed();
        $embded->setTitle(Main::$lang->get("title"));
        $embded->setDescription(Main::$lang->get("end-maintenance"));
        $embded->addField(Main::$lang->get("date"), date("F j, Y, g:i a"));
        $messagesend->addEmbed($embded);
        $send->send($messagesend);
    }
}