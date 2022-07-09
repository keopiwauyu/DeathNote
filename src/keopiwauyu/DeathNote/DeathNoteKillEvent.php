<?php

declare(strict_types=1);

namespace keopiwauyu\DeathNote;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\item\WritableBookBase;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class DeathNoteKillEvent extends PluginEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Plugin $plugin, private DeathNoteEnchant $ench, private WritableBookBase $note, private Player $killer, private Player $target) {
        parent::__construct($plugin);
    }

    public function getEnch() : DeathNoteEnchant {
        return $this->ench;
    }

    public function getNote() : WritableBookBase {
        return $this->note;
    }

    public function getKiller() : Player {
        return $this->killer;
    }

    public function getTarget() : Player {
        return $this->target;
    }
}