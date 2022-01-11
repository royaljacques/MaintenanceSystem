<?php
namespace royal\maintenanceSystem;
use pocketmine\event\Listener;
use pocketmine\lang\Translatable;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {


    public static Config $config;
    public static self $instance;

    protected function onEnable (): void
    {
        self::$instance = $this;
        var_dump("TTTTTTTTTTTTTTTTTTTTTTTTTTTTTTT");
        DefaultPermissions::registerPermission(new Permission("maintenance.use", "desc", [DefaultPermissions::ROOT_OPERATOR]));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register(".", new MaintenanceCommands("maintenance", "activate maintenance in this server", "/maintenance help"));
        $dir = Main::getInstance()->getServer()->getDataPath()."server.properties";


        self::$config = new Config($this->getDataFolder()."config.yml", Config::YAML, array(
            "maintenance-announce"=>"Une maintenance va débuter dans: {time}",
            "secound-announce"=> [
                30,
                20,
                10,
                5
            ],
            "tranfere-to-other-server"=>true,
            "ip-server"=> "127.0.0.1",
            "port-server" => "19132",
            "message-to-kick"=> "you have bean kicked for Maintenance server",
            "form-reason" => "raison"

        ));
    }
    public static function getInstance():self{
        return self::$instance;
    }
}