<?php

namespace nlog\NLOGSmartUI\FormHandler;

use nlog\NLOGSmartUI\Main;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;
use pocketmine\level\Position;

class MainForm {
	
	/** @var Main */
	protected $owner;
	
	/** @var Player */
	protected $player;
	
	public function __construct(Main $owner, Player $player) {
		$this->owner = $owner;
		$this->player = $player;
	}
	
	public function sendPacket() {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->owner->MainID;
		$pk->formData = $this->getFormData();
		
		$this->player->dataPacket($pk);
	}
	
	public function onRecieve($result) {
		if ($result) {
			(new MenuForm($this->owner, $this->player))->sendPacket();
			return;
		}elseif (!$result) {
			$world = $this->owner->getServer()->getDefaultLevel()->getSpawnLocation();
			$this->player->teleport(new Position($world->x, $world->y, $world->z, $this->owner->getServer()->getDefaultLevel()));
			$this->player->sendMessage($this->owner->tag . "스폰으로 이동하였습니다.");
			return;
		}else{
			return;
		}
	}
	
	public function getFormData() {
		$json = [];
		$json["type"] = "modal";
		$json["title"] = "- 메인 메뉴";
		$json["content"] = $this->owner->setting->getMessage($this->player);
		$json["button1"] = ">> 메뉴 보기 <<"; //true
		$json["button2"] = ">> 스폰 이동 <<"; //false
		
		return json_encode($json);
	}
	
}