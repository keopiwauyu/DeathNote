<?php

declare(strict_types=1);

namespace keopiwauyu\DeathNote;

use DaPigGuy\PiggyCustomEnchants\PiggyCustomEnchants;
use DaPigGuy\PiggyCustomEnchants\enchants\ReactiveEnchantment;
use SOFe\AwaitGenerator\Await;
use pocketmine\Server;
use pocketmine\event\Event;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerEditBookEvent;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\WritableBookBase;
use pocketmine\item\enchantment\Rarity;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class DeathNoteEnchant extends ReactiveEnchantment
{
    public const NAME = "Death Note";
    public string $name = self::NAME;
    public int $rarity = Rarity::UNCOMMON;
    public int $maxLevel = 1;

    public function __construct(private Plugin $pl, int $id) {
        $pce = null;
        foreach (Server::getInstance()->getPluginManager()->getPlugins() as $plugin) {
            if ($plugin instanceof PiggyCustomEnchants) $pce = $plugin;
        }
        parent::__construct($pce ?? throw new \RuntimeException("Cannot get PiggyCustomEnchants plugin main instance"), $id);
    }

    public function react(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void
    {
        if (!$item instanceof WritableBookBase) return;

        $names = [];
        foreach ($item->getPages() as $page) $names = [...$names, ...explode("\n", $page->getText())];
        $names = array_unique($names);

        foreach ($names as $name) {
            $target = Server::getInstance()->getPlayerExact($name);
            if ($target !== null) {
                $name = $target->getName();
                $kill = false;
            foreach ($this->getRemembers($item) as $remember) {
                if (!$remember instanceof CompoundTag) continue;
                if ($this->getRememberName($remember) === $name) {
                    $kill = true;
                    break;
                }
            }
            }
            if (!isset($target) || !($kill ?? throw new \AssertionError("unreachable"))) {
                $player->sendMessage("Who is $name?");
                continue;
            }

            $event = new DeathNoteKillEvent($this->pl, $this, $item, $player, $target);
            $event->call();
            if ($event->isCancelled()) continue;
            $target->kill();
        }
        // TODO: book duration??
    
    }

    private function getData(Item $item) : CompoundTag {
        return $item->getNamedTag()->getCompoundTag("DeathNote") ?? new CompoundTag();
    }

    private function getRemembers(Item $item) : ListTag {
        return $this->getData($item)->getListTag("remember") ?? new ListTag([], NBT::TAG_Compound);
    }

    private function getRememberName(CompoundTag $tag) : string {
        return $tag->getString("name");
    }

    /**
     * @phpstan-return class-string<Event>[]
     */
    public function getReagent() : array {
        return [PlayerEditBookEvent::class];
    }
}