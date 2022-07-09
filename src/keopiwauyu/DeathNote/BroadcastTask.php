<?php

declare(strict_types=1);

namespace keopiwauyu\DeathNote;

use pocketmine\scheduler\Task;
use pocketmine\Server;

class BroadcastTask extends Task{

	public function __construct(private Server $server){ }

	public function onRun() : void{
		$this->server->broadcastMessage("[DeathNote] I've run on tick " . $this->server->getTick());
	}
}
