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
 * Copyright (C) 2020-2021 Kygekraqmak, KygekTeam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

declare(strict_types=1);

namespace Kygekraqmak\KygekPingTPS;

use KygekTeam\KtpmplCfs\KtpmplCfs;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class Main extends PluginBase {

	private bool $isDev = false;
	public static string $PREFIX;

	private static self $instance;

	public function onEnable(): void {
		self::$instance = $this;
		$ktpmplCfs = new KtpmplCfs($this);
		$this->saveDefaultConfig();
		if ($this->isDev) {
			$ktpmplCfs->warnDevelopmentVersion();
		}
		$ktpmplCfs->checkUpdates();
		$ktpmplCfs->checkConfig("2.0");
		self::$PREFIX = $this->getConfig()->get("prefix");
	}

	public static function getInstance(): self {
		return self::$instance;
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
		$this->getConfig()->reload();
		switch ($cmd->getName()) {
			case "tps":
				$tps = new TPS();
				$tps->TPSCommand($sender, $cmd, $label, $args);
				break;
			case "ping":
				$ping = new Ping();
				$ping->PingCommand($sender, $cmd, $label, $args);
				break;
		}
		return true;
	}

	public static function replace($string): string {
		$replace = [
			"{prefix}" => self::$PREFIX,
			"&" => "§"
		];
		return strtr($string, $replace);
	}
}
