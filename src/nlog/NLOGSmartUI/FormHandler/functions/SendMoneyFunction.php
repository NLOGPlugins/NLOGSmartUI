<?php

namespace nlog\NLOGSmartUI\FormHandler\functions;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use onebone\economyapi\EconomyAPI;

class SendMoneyFunction extends FormFunction {
	
	public static function getName() {
		return "돈 보내기";
	}
	
	public function sendPacket() {
		$data = $this->getFormData();
		
		if (!$data) {
			$this->player->sendMessage($this->owner->tag . "해당 플레이어는 돈을 보낼 수 없습니다.");
			return;
		}
		
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->owner->SendMoneyID;
		$pk->formData = $this->getFormData();
		
		$this->player->dataPacket($pk);
	}
	
	public function onRecieve($result) {
		if ($result === null) {
			unset($this->owner->economy[$this->player->getName()]);
			return;
		}
		
		$result = json_decode($result, true);
		
		$money = (int) $result[1];
		$response = EconomyAPI::getInstance()->reduceMoney($this->player, $money);
		if ($response === EconomyAPI::RET_INVALID) {
			$this->player->sendMessage($this->owner->tag . "돈의 액수를 0 이하로 입력하시거나 돈이 부족합니다.");
		}elseif ($response === EconomyAPI::RET_SUCCESS) {
			EconomyAPI::getInstance()->addMoney($this->owner->economy[$this->player->getName()][$result[0]], $money);
			
			$this->log($this->player->getName(), $this->owner->economy[$this->player->getName()][$result[0]], $money);
			
			$orgin = (int) EconomyAPI::getInstance()->myMoney($this->player);
			$this->player->sendMessage($this->owner->tag . "정상적으로 돈을 보내었습니다.");
			$this->player->sendMessage($this->owner->tag . "원래 돈 : " . $orgin + $money . "  보낸 돈 : {$money}  남은 돈 : " . EconomyAPI::getInstance()->myMoney($this->player));
			
		}
	}
	
	public function log($SendPlayer, $RecievePlayer, $money) {
		$time = date("Y-m-d H:i:s");
		
		if (!isset($this->owner->moneyloggerarray[$RecievePlayer])) {
			$this->owner->moneyloggerarray[$RecievePlayer] = [];
		}
		
		$this->owner->moneyloggerarray[$RecievePlayer][] = ["time" => $time, "sendplayer" => $SendPlayer, "money" => $money];
		
		$this->owner->moneylogger->setAll($this->owner->moneyloggerarray);
		$this->owner->moneylogger->save();
	}
	
	public function getFormData() {
		$player = $this->player;
		$list = EconomyAPI::getInstance()->getAllMoney();
		
		if (empty($list)) {
			return false;
		}
		
		$money = [];
		
		foreach($list as $name => $price) {
			$money[] = $name;
		}
		
		$json = [];
		$json["type"] = "custom_form";
		$json["title"] = "돈 보내기";
		$json["content"] = [];
		$json["content"][] = ["type" => "dropdown", "text" => "돈 보낼 플레이어 선택", "options" => $money];
		$json["content"][] = ["type" => "input", "text" => "돈 액수", "placeholder" => "돈 액수"];
		
		$this->owner->economy[$player->getName()] = $money;
		
		return json_encode($json);
	}
	
}