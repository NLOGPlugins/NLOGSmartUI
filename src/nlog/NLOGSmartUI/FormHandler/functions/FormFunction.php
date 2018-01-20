<?php

namespace nlog\NLOGSmartUI\FormHandler\functions;

use pocketmine\Player;
use nlog\NLOGSmartUI\Main;

abstract class FormFunction {
	
	/** @var Main */
	protected $owner;
	
	/** @var Player */
	protected $player;
	
	public function __construct(Main $owner, Player $player) {
		$this->owner = $owner;
		$this->player = $player;
	}
	
	abstract public function sendPacket();
	
	abstract public function onRecieve($result);
	
	abstract public function getFormData();
	
	abstract public static function getName();
	
}
