<?php

namespace nlog\NLOGSmartUI\FormHandler\functions;

use pocketmine\Player;
use nlog\NLOGSmartUI\Main;

class FormFunction {
	
	/** @var Main */
	protected $owner;
	
	/** @var Player */
	protected $player;
	
	public function __construct(Main $owner, Player $player) {
		$this->owner = $owner;
		$this->player = $player;
	}
	
	public function sendPacket() {
		
	}
	
	public function onRecieve($result) {
		
	}
	
	public function getFormData() {
		
	}
	
	public static function getName() {
		
	}
	
}