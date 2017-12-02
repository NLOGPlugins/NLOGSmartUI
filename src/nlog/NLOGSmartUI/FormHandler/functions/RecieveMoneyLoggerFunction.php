<?php

namespace nlog\NLOGSmartUI\FormHandler\functions;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use nlog\NLOGSmartUI\FormHandler\MenuForm;

class RecieveMoneyLoggerFunction extends FormFunction {
	
	public static function getName() {
		return "받은 돈 보기";
		//return "Recieve Money Logger Menu";
	}
	
	public function sendPacket() {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->owner->RecieveMoneyLoggerID;
		$pk->formData = $this->getFormData();
		
		$this->player->dataPacket($pk);
	}
	
	public function onRecieve($result) {
		$result = json_decode($result, true);
		if ($result) {
			(new MenuForm($this->owner, $this->player))->sendPacket();
			return;
		}
	}
	
	public function getInformation($RecievePlayer) {
		if (empty($this->owner->moneyloggerarray)) {
			return "아무한테도 돈을 받지 않았습니다.";
		}
		$output = "";
		foreach ($this->owner->moneyloggerarray[strtolower($RecievePlayer)] as $time => $array) {
			$output .= "[" . $array["time"] ."] <" . $array["sendplayer"] . "> 돈 : " . $array["money"] . "\n";
		}
		return $output;
	}
	
	public function getFormData() {
		$player = $this->player;
		$json = [];
		$json["type"] = "modal";
		$json["title"] = "받은 돈 확인";
		$json["content"] = $this->getInformation($player->getName());
		$json["button1"] = "메뉴로 돌아가기"; //true
		$json["button2"] = "닫기"; //false
		
		return json_encode($json);
	}
	
}