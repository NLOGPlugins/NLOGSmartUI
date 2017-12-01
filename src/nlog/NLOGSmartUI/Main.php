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
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Utils;
use pocketmine\command\PluginCommand;

class Main extends PluginBase implements Listener {
	
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
	
	
	
	/** @var string */
	private $link = "https://raw.githubusercontent.com/NLOGPlugins/NLOGSmartUI/master/version.json";
	
	#====================================================
	
	/** @var int */
	public $MainID = 5215;
	
	/** @var int */
	public $MenuID = 4315;
	
	#====================================================
	
	/** @var int */
	public $WarpID = 8472;
	
	/** @var int */
	public $SendMoneyID = 3837;
	
	/** @var int */
	public $RecieveMoneyLoggerID = 8476;
	
	/** @var int */
	public $AreaMoveID = 5648;
	
	/** @var int */
	public $MonthCalendarID = 9373;
	
	/** @var int */
	public $PluginInfoID = 3736;
	
	#====================================================
	
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
	
	protected function checkUpdate(): bool {
		$url = json_decode(Utils::getURL($this->link), true);
		
		if (!isset($url["last_ver"])) {
			$this->getLogger()->alert("인터넷에 연결이 되지 않아 업데이트 확인을 할 수 없습니다.");
			return false;
		}
		
		$last_ver = $url["last_ver"];
		$notice = $url["notice"];
		$force = $url["require_force_update"];
		$update_notice = $url["update_notice"][$this->getDescription()->getVersion()] ?? "";
		$changelog = "";
		foreach ($url["changelog"] as $ver => $content) {
			$content = trim($content);
			$changelog .= "v$ver : $content";
		}
		
		if ($notice !== "") {
			$this->getLogger()->alert("공지 사항 : " . $notice);
		}
		
		if (version_compare($this->getDescription()->getVersion(), $last_ver) < 0) {
			$this->getLogger()->alert("새로운 버전이 출시되었습니다. 현재 버전 : " . $this->getDescription()->getVersion() . " 최신 버전 : " . $last_ver);
			if ($update_notice !== "") {
				$this->getLogger()->alert("업데이트 공지사항 : " . $update_notice);
			}
			if ($force) {
				$this->getLogger()->alert("업데이트를 하지 않으면 플러그인을 사용할 수 없습니다.");
				$this->getPluginLoader()->disablePlugin($this);
				return false;
			}
			return false;
		}
		
		$this->getLogger()->notice("최신 버전입니다.");
		return true;
	}
	
	protected function registerCommand(string $name, string $permission, string $description = "", string $usage = "", bool $force = false, array $aliases= []) {
  	if ($force && $this->getServer()->getCommandMap()->getCommand($name) instanceof Command) {
  		$this->getServer()->getCommandMap()->getCommand($name)->unregister($this->getServer()->getCommandMap());
  	}
  	$command = new PluginCommand($name, $this);
  	$command->setLabel($name);
  	$command->setPermission($permission);
  	$command->setDescription($description);
  	$command->setUsage($usage);
  	$command->setAliases($aliases);
  	
  	$this->getServer()->getCommandMap()->register($this->getDescription()->getName(), $command);
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
