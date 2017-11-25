<?php

namespace nlog\NLOGSmartUI\FormHandler\functions;

use nlog\NLOGSmartUI\Main;
use pocketmine\Player;
use solo\swarp\SWarp;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class WarpFunction extends FormFunction {
	
	/** @var SWarp */
	private $swarp;
	
	public function __construct(Main $owner, Player $player, SWarp $swarp) {
		parent::__construct($owner, $player);
		$this->swarp = $swarp->getInstance();
	}
	
	public static function getName() {
		return "워프";
	}
	
	public function sendPacket() {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->owner->WarpID;
		if (!$this->getFormData()) {
			$this->player->sendMessage($this->owner->tag . "워프가 서버에 없습니다.");
			return;
		}
		$pk->formData = $this->getFormData();
		
		$this->player->dataPacket($pk);
	}
	
	public function onRecieve($result) {
		if ($result == null) {
			unset($this->owner->warp[$this->player->getName()]);
			return;
		}
		$result = json_decode($result, true);
		if ($this->swarp->getWarp($this->owner->warp[$this->player->getName()][$result]) === null) {
			$this->player->sendMessage(self::TAG . "워프가 존재하지 않습니다.");
		}else{
			$this->swarp->getWarp($this->owner->warp[$this->player->getName()][$result])->warp($this->player);
		}
		unset($this->owner->warp[$this->player->getName()]);
	}
	
	public function getFormData() {
		$player = $this->player;
		
		$json = [];
		$json["type"] = "form";
		$json["title"] = "워프";
		$json["content"] = "워프할 곳을 선택하세요.\nMade By NLOG";
		$json["buttons"] = [];
		
		$this->owner->warp[$player->getName()] = [];
		
		if (empty($this->swarp->getInstance()->getAllWarp())) {
			return false;
		}
		
		foreach ($this->swarp->getAllWarp() as $name => $warp) {
			$json["buttons"][] = ["text" => $warp->getName()];
			$this->owner->warp[$player->getName()][] = $warp->getName();
		}
		
		return json_encode($json);
	}
	
}