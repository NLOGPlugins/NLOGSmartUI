<?php

namespace nlog\NLOGSmartUI;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\event\player\PlayerInteractEvent;
use nlog\NLOGSmartUI\FormHandler\MainForm;
use nlog\NLOGSmartUI\FormHandler\functions\WarpFunction;
use nlog\NLOGSmartUI\FormHandler\functions\SendMoneyFunction;
use nlog\NLOGSmartUI\FormHandler\functions\RecieveMoneyLoggerFunction;
use nlog\NLOGSmartUI\FormHandler\functions\AreaMoveFunction;
use solo\swarp\SWarp;
use nlog\NLOGSmartUI\FormHandler\MenuForm;
use pocketmine\utils\Config;
use nlog\NLOGSmartUI\FormHandler\functions\MonthCalendarFunction;
use nlog\NLOGSmartUI\FormHandler\functions\PluginInfoFunction;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;

class Main extends FormsID implements Listener {
	
	/** @var Settings */
	public $setting;
	
	#====================================================
	/** @var string */
	public $tag = "§b§o[ SmartUI ] §7";
	
	/** @var array */
	public $warp = [];
	
	/** @var array */
	public $economy = [];
	
	/** @var Config */
	public $moneylogger;
	
	/** @var array */
	public $moneyloggerarray = [];
	
	/** @var array */
	public $simplearea = [];
	
	public function onEnable() {
		$this->checkUpdate();
		
		@mkdir($this->getDataFolder());
		$this->setting = new Settings($this->getDataFolder() . "setting.yml");
		$this->moneylogger = new Config($this->getDataFolder() . "moneylogger.yml", Config::YAML);
		$this->moneyloggerarray = $this->moneylogger->getAll();
		$this->warp = [];
		
		$this->registerCommand("ui", true, "SmartUI를 실행합니다.", "/ui", true);
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("SmartUI 플러그인 활성화");
	}
	
	public function onDataRecieve(DataPacketReceiveEvent $ev) {
		$pk = $ev->getPacket();
		if ($pk instanceof ModalFormResponsePacket) {
			switch ($pk->formId) {
				case $this->MainID:
					$result = json_decode($pk->formData, true);
					(new MainForm($this, $ev->getPlayer()))->onRecieve($result);
					break;
				case $this->MenuID:
					(new MenuForm($this, $ev->getPlayer()))->onRecieve($pk->formData);
					break;
				case $this->WarpID:
					(new WarpFunction($this, $ev->getPlayer(), SWarp::getInstance()))->onRecieve($pk->formData);
					break;
				case $this->SendMoneyID:
					(new SendMoneyFunction($this, $ev->getPlayer()))->onRecieve($pk->formData);
					break;
				case $this->RecieveMoneyLoggerID:
					(new RecieveMoneyLoggerFunction($this, $ev->getPlayer()))->onRecieve($pk->formData);
					break;
				case $this->AreaMoveID:
					(new AreaMoveFunction($this, $ev->getPlayer()))->onRecieve($pk->formData);
					break;
				case $this->MonthCalendarID:
					(new MonthCalendarFunction($this, $ev->getPlayer()))->onRecieve($pk->formData);
					break;
				case $this->PluginInfoID:
					(new PluginInfoFunction($this, $ev->getPlayer()))->onRecieve($pk->formData);
					break;
				default:
					return;
			}
		}
	}
	
	public function onCommand(CommandSender $sender,Command $command,string $label,array $args): bool {
		if (!$sender instanceof Player) {
			$sender->sendMessage($this->tag . "콘솔에서는 명령어를 입력하실 수 없습니다.");
			return true;
		}
		(new MainForm($this, $sender))->sendPacket();
		return true;
	}
	
	public function onInteract (PlayerInteractEvent $ev) {
		if ($ev->getItem()->getId() . ":" . $ev->getItem()->getDamage() == $this->setting->getItem()) {
			(new MainForm($this, $ev->getPlayer()))->sendPacket();
		}
	}
	
	
} //클래스 괄호

?>
