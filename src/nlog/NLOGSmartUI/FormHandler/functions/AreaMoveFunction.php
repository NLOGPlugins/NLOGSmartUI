<?php

namespace nlog\NLOGSmartUI\FormHandler\functions;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use ifteam\SimpleArea\database\area\AreaProvider;
use ifteam\SimpleArea\database\user\UserProperties;
use pocketmine\level\Position;

class AreaMoveFunction extends FormFunction {
	
	public static function getName() {
		return "섬 이동";
	}
	
	public function sendPacket() {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->owner->AreaMoveID;
		if (!$this->getFormData()) {
			$this->player->sendMessage($this->owner->tag . "섬을 소유하고 있지 않습니다.");
			return;
		}
		$pk->formData = $this->getFormData();
		
		$this->player->dataPacket($pk);
	}
	
	public function onRecieve($result) {
		$player = $this->player;
		
		if ($result === null) {
			unset($this->owner->simplearea[$this->player->getName()]);
			return;
		}
		$result = json_decode($result, true);
		if (AreaProvider::getInstance()->getAreaToId("island", $this->owner->simplearea[$this->player->getName()][$result]) === null) {
			$this->player->sendMessage($this->owner->tag . "섬이 존재하지 않습니다.");
		}else{
			$vec = AreaProvider::getInstance()->getAreaToId("island", $this->owner->simplearea[$player->getName()][$result])->getCenter();
			$this->player->teleport(new Position($vec->x, $vec->y, $vec->z, $this->owner->getServer()->getLevelByName("island")));
			$this->player->sendMessage($this->owner->tag . $this->owner->simplearea[$player->getName()][$result] . "번 섬으로 이동하였습니다.");
		}
		unset($this->owner->simplearea[$this->player->getName()]);
	}
	
	public function getFormData() {
		$player = $this->player;
		
		$json = [];
		$json["type"] = "form";
		$json["title"] = "섬 이동";
		$json["content"] = "이동할 섬을 선택하세요";
		$json["buttons"] = [];
		
		$this->owner->simplearea[$player->getName()] = [];
		
		$island = UserProperties::getInstance()->getUserProperties($player->getName(), "island");
		
		if (empty($island)) {
			return false;
		}
		
		foreach ($island as $name => $cordinates) {
			$json["buttons"][] = ["text" => (string) $name];
			$this->owner->simplearea[$player->getName()][] = $name;
		}
		
		return json_encode($json);
	}
	
}