<?php

namespace nlog\NLOGSmartUI;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Utils;

use pocketmine\command\PluginCommand;

class FormsID extends PluginBase {
	
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
	
	protected function checkUpdate(): bool {
		$url = json_decode(Utils::getURL($this->link), true);
		
		if (!isset($url["last_ver"])) {
			$this->getLogger()->alert("인터넷에 연결이 되지 않아 업데이트 확인을 할 수 없습니다.");
			return false;
		}
		
		$last_ver = $url["last_ver"];
		$notice = $url["notice"];
		$force = $url["require_force_update"];
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
	
}