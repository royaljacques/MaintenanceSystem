<?php

namespace royal\maintenanceSystem;

//     _          _    _    _____
//    / \    ___ | |_ | |__|_   _|___   __ _  _ __ ___
//   / _ \  / _ \| __|| '_ \ | | / _ \ / _` || '_ ` _ \
//  / ___ \|  __/| |_ | | | || ||  __/| (_| || | | | | |
// /_/   \_\\___| \__||_| |_||_| \___| \__,_||_| |_| |_|
//
//                            _
//  _ __  ___   _   _   __ _ | |
// | '__|/ _ \ | | | | / _` || |
// | |  | (_) || |_| || (_| || |
// |_|   \___/  \__, | \__,_||_|
//              |___/

use pocketmine\event\Listener;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{


    public static Config $config;
    public static Config $lang;
    public static self $instance;

    protected function onEnable (): void
    {
        $this->saveResource("fr.FR.yml");
        self::$instance = $this;
        DefaultPermissions::registerPermission(new Permission("maintenance.use", "desc", [DefaultPermissions::ROOT_OPERATOR]));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register(".", new MaintenanceCommands("maintenance", "activate maintenance in this server", "/maintenance help"));
        self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array(
            "maintenance-announce" => "Une maintenance va débuter dans: {time}",
            "secound-announce" => [
                30,
                20,
                10,
                5
            ],
            "tranfere-to-other-server" => true,
            "ip-server" => "127.0.0.1",
            "port-server" => "19132",
            "message-to-kick" => "you have bean kicked for Maintenance server",
            "form-reason" => "raison"

        ));
        self::$lang = new Config($this->getDataFolder()."fr.FR.yml");
    }

    public static function getInstance (): self
    {
        return self::$instance;
    }
}