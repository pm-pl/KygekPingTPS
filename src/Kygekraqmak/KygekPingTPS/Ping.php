<?php

/**
 *     _    __                  _                                     _
 *    | |  / /                 | |                                   | |
 *    | | / /                  | |                                   | |
 *    | |/ / _   _  ____   ____| | ______ ____   _____ ______   ____ | | __
 *    | |\ \| | | |/ __ \ / __ \ |/ /  __/ __ \ / __  | _  _ \ / __ \| |/ /
 *    | | \ \ \_| | <__> |  ___/   <| / | <__> | <__| | |\ |\ | <__> |   <
 * By |_|  \_\__  |\___  |\____|_|\_\_|  \____^_\___  |_||_||_|\____^_\|\_\
 *              | |    | |                          | |
 *           ___/ | ___/ |                          | |
 *          |____/ |____/                           |_|
 *
 * A PocketMine-MP plugin to see the server TPS and a player's ping
 * Copyright (C) 2020-2021 Kygekraqmak
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
*/

declare(strict_types=1);

namespace Kygekraqmak\KygekPingTPS;

use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class Ping {

	public $other;
	public $pingother;
	public $usage;

	private function getConfig() : Config {
	    return Main::getInstance()->getConfig();
    }

	public function PingCommand(CommandSender $sender, Command $cmd, string $label, array $args) {
		$this->usage = Main::PREFIX . TF::WHITE . "Usage: /ping <player>";
		if (isset($args[0])) {
			$this->other = Main::getInstance()->getServer()->getPlayer($args[0]);
			if ($this->other == null) {
				$sender->sendMessage($this->getPlayerNotFoundMessage());
				return;
			}
			$this->pingother = TF::AQUA . $this->other->getPing();
		}
		if (!$sender instanceof Player) {
			if (count($args) < 1) $sender->sendMessage($this->usage);
			elseif (isset($args[0])) {
				if (!$this->other instanceof Player) $sender->sendMessage($this->getPlayerNotFoundMessage());
				else $sender->sendMessage($this->getPlayerPingMessage($this->other, false));
			}
		} else {
			if ($sender->hasPermission("kygekpingtps.ping")) {
				if (count($args) < 1) $sender->sendMessage($this->getPlayerPingMessage($sender, true));
				elseif (isset($args[0])) {
					if (!$this->other instanceof Player) $sender->sendMessage($this->getPlayerNotFoundMessage());
					else $sender->sendMessage($this->getPlayerPingMessage($this->other, false));
				}
			} else {
			    $sender->sendMessage($this->getNoPermMessage());
            }
		}
	}

	private function getNoPermMessage() : string {
        $noperm = $this->getConfig()->get("no-permission", "");
        $noperm = Main::replace($noperm);
        return empty($noperm) ? Main::PREFIX . TF::RED . "You do not have permission to use this command" : $noperm;
    }

    private function getPlayerPingMessage(Player $player, bool $self = true) : string {
	    $playerping = $this->getConfig()->get("player-ping", "");
	    $playername = $self ? "Your" : $player->getName() . "'s";
	    $playerping = str_replace(["{player}", "{ping}"], [$playername, $player->getPing()], Main::replace($playerping));
	    return empty($playerping) ? Main::PREFIX . TF::GREEN . $playername . " current ping: " . TF::AQUA .
            ($self ? $player->getPing() : $this->pingother) : $playerping;
    }

    private function getPlayerNotFoundMessage() : string {
        $notfound = $this->getConfig()->get("player-not-found", "");
        $notfound = Main::replace($notfound);
        return empty($notfound) ? Main::PREFIX . TF::RED . "Player was not found" : $notfound;
    }

}
