<?php

namespace nlog\NLOGSmartUI;

use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\Player;
use onebone\economyapi\EconomyAPI;

class Settings {
	
	/** @var Config */
	protected $config;
	
	/** @var Server */
	protected $server;
	
	/** @var array */
	protected $availableParameter;
	
	public function __construct(string $path) {
		$this->config = new Config($path, Config::YAML);
		$this->server = Server::getInstance();
		$this->availableParameter = [
				"@playername", "@playercount", "@playermaxcount", "@motd", "@mymoney", "@health", "@maxhealth", "@year", "@month", "@day", "@hour"
		];
		
		$this->init();
	}
	
	protected function init() {
		if (!$this->config->get("item")) {
			$this->config->set("item", "345:0");
			$this->save();
		}
		if (!$this->config->get("message") || is_array($this->config->get("message"))) {
			$this->config->set("message", "서버인원 : @playercount/@playermaxcount\n내 돈 : @mymoney\n내 체력 : @health/@maxhealth\n오늘은 @year년 @month월 @day일 입니다.");
			$this->save();
		}
	}
	
	public final function getConfig(): Config {
		return $this->config;
	}
	
	public final function save(bool $async = false) {
		$this->config->save($async);
	}
	
	public function getItem() {
		/*if (!explode(":", $this->config->get("item"))) {
			return $this->config->get("item") . ":0";
		}*/
		return $this->config->get("item");
	}
	
	public function getMessage(Player $player, $economy = null) {
		if (!$economy instanceof EconomyAPI) {
			$economy = EconomyAPI::getInstance();
		}
		$msg = $this->config->get("message");
		$msg = str_replace($this->availableParameter, [
				$player->getName(), 
				count($this->server->getOnlinePlayers()), 
				$this->server->getMaxPlayers(),
				$this->server->getNetwork()->getName(),
				$economy->myMoney($player),
				$player->getHealth(),
				$player->getMaxHealth(),
				date("Y"),
				date("m"),
				date("d"),
				date("g")
		], $msg);
		
		$msg = str_replace('\n', "\n", $msg);
		
		return $msg;
	}
	
	public function getTitle(Player $player, $economy = null) {
		if (!$economy instanceof EconomyAPI) {
			$economy = EconomyAPI::getInstance();
		}
		$msg = $this->config->get("message");
		$msg = str_replace($this->availableParameter, [
				$player->getName(),
				count($this->server->getOnlinePlayers()),
				$this->server->getMaxPlayers(),
				$this->server->getNetwork()->getName(),
				$economy->myMoney($player),
				$player->getHealth(),
				$player->getMaxHealth(),
				date("Y"),
				date("m"),
				date("d"),
				date("g")
		], $msg);
		
		$msg = str_replace('\n', "\n", $msg);
		
		return $msg;
	}
}
