<?php

namespace Electro\NoVoid;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\event\Listener;

class NoVoid extends PluginBase implements Listener{

    public array $worlds = [];
    public bool $whitelist = true;
    public bool $toDefault;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if ($this->getConfig()->get("Mode") !== "Whitelist" && !$this->getConfig()->get("Mode") !== "whitelist")
        {
            $this->whitelist = false;
        }
        foreach ($this->getConfig()->get("Worlds") as $world)
        {
            $this->worlds[] = $world;
        }
        $this->toDefault = $this->getConfig()->get("ToDefaultWorld");
    }

    public function onDamage(EntityDamageEvent $event)
    {
        $player = $event->getEntity();
        $cause = $event->getCause();

        if (!$player instanceof Player) return true;
        if ($cause !== EntityDamageEvent::CAUSE_VOID) return true;

        if ($this->whitelist && in_array($player->getWorld()->getFolderName(), $this->worlds))
        {
            if ($this->toDefault)
            {
                $player->teleport($this->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
                $event->cancel();
                return true;
            }
            $player->teleport($player->getWorld()->getSpawnLocation());
            $event->cancel();
        }
        if (!$this->whitelist && !in_array($player->getWorld()->getFolderName(), $this->worlds))
        {
            if ($this->toDefault)
            {
                $player->teleport($this->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
                $event->cancel();
                return true;
            }
            $player->teleport($player->getWorld()->getSpawnLocation());
            $event->cancel();
        }
    }
}
