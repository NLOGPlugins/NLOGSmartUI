<?php

namespace nlog\NLOGSmartUI\FormHandler;

use nlog\NLOGSmartUI\Main;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;
use nlog\NLOGSmartUI\FormHandler\functions\WarpFunction;
use solo\swarp\SWarp;
use nlog\NLOGSmartUI\FormHandler\functions\SendMoneyFunction;
use nlog\NLOGSmartUI\FormHandler\functions\RecieveMoneyLoggerFunction;
use nlog\NLOGSmartUI\FormHandler\functions\AreaMoveFunction;
use nlog\NLOGSmartUI\FormHandler\functions\MonthCalendarFunction;
use nlog\NLOGSmartUI\FormHandler\functions\PluginInfoFunction;

class MenuForm {
	
	/** @var Main */
	protected $owner;
	
	/** @var Player */
	protected $player;
	
	/** @var array */
	private $option = [];
	
	public function __construct(Main $owner, Player $player) {
		$this->owner = $owner;
		$this->player = $player;
		
		$this->option = [WarpFunction::getName(), SendMoneyFunction::getName(), RecieveMoneyLoggerFunction::getName(), AreaMoveFunction::getName(), MonthCalendarFunction::getName(), PluginInfoFunction::getName()];
		
		/*$option = [WarpFunction::getName(), SendMoneyFunction::getName(), RecieveMoneyLoggerFunction::getName()];
		foreach ($option as $k => $v) {
			$this->option[] = "§7 > " . $v;
		}*/
	}
	
	public function sendPacket() {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->owner->MenuID;
		$pk->formData = $this->getFormData();
		
		$this->player->dataPacket($pk);
	}
	
	public function onRecieve($result) {
		$result = json_decode($result, true);
		
		if ($result === null) {
			return;
		}
		//$class = "nlog\\NLOGSmartUI\\FormHandler\\";
		switch ($this->option[$result]) {
			case WarpFunction::getName():
				(new WarpFunction($this->owner, $this->player, SWarp::getInstance()))->sendPacket();
				break;
			case SendMoneyFunction::getName():
				(new SendMoneyFunction($this->owner, $this->player))->sendPacket();
				break;
			case RecieveMoneyLoggerFunction::getName():
				(new RecieveMoneyLoggerFunction($this->owner, $this->player))->sendPacket();
				break;
			case AreaMoveFunction::getName():
				(new AreaMoveFunction($this->owner, $this->player))->sendPacket();
				break;
			case MonthCalendarFunction::getName():
				(new MonthCalendarFunction($this->owner, $this->player))->sendPacket();
				break;
			case PluginInfoFunction::getName():
				(new PluginInfoFunction($this->owner, $this->player))->sendPacket();
				break;
		}
	}
	
	public function getFormData() {
		$json = [];
		$json["type"] = "form";
		$json["content"] = "";
		$json["title"] = "- 기능 선택 메뉴";
		$json["buttons"] = [];
		
		foreach ($this->option as $text) {
			$json["buttons"][] = ["text" => $text];
		}
		
		return json_encode($json);
	}
	
}