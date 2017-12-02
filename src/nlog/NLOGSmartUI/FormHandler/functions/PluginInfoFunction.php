<?php

namespace nlog\NLOGSmartUI\FormHandler\functions;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\utils\Utils;
use nlog\NLOGSmartUI\FormHandler\MenuForm;

class PluginInfoFunction extends FormFunction {
	
	/** @var string */
	private $link = "https://raw.githubusercontent.com/NLOGPlugins/NLOGSmartUI/master/version.json";
	
	public static function getName() {
		return "플러그인 정보 보기";
	}
	
	public function sendPacket() {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->owner->PluginInfoID;
		$pk->formData = $this->getFormData();
		
		$this->player->dataPacket($pk);
	}
	
	public function onRecieve($result) {
		$result = json_decode($result, true);
		if ($result !== null) {
			(new MenuForm($this->owner, $this->player))->sendPacket();
			return;
		}
	}
	
	public function getText() {
		$output = "";
		
		$url = json_decode(Utils::getURL($this->link), true);
		if (!isset($url["last_ver"])) {
			return "인터넷과 연결되어 있지 않습니다.";
		}
		
		$last_ver = $url["last_ver"];
		$notice = $url["notice"];
		$changelog = "";
		foreach ($url["changelog"] as $ver => $content) {
			$content = trim($content);
			$changelog .= "v$ver : $content";
		}
		
		
		/*$output .= "현재 플러그인의 버전은 §b" . $this->owner->getDescription()->getVersion() . "§r 입니다.\n";
		$output .= "이 플러그인의 최신 버전은 §b" . $last_ver . "§r 입니다.\n\n";
		$output .= "이 플러그인의 제작자는 엔로그(NLOG)입니다.\n\n";
		$output .= "이 플러그인의 소스 자체의 판매 행위는 금지합니다.\n";
		$output .= "하지만, 소스 자체의 판매 행위가 아닌, 플러그인 적용 후 서버 후원 등은 허용됩니다.\n";
		$output .= "다른 기능들을 삭제 혹은 제한적인 수정할 수는 있지만, 이 기능§o(플러그인 정보 보기 기능)§r을 삭제 혹은 내용을 수정하는 것은 프로그램의 주요 소스를 수정하는 행위로 간주하여 저작물 2차 무단 수정으로 간주합니다.\n";
		$output .= "이 플러그인 내의 기능을 수정하는 경우에는 모두 저작자에게 허락을 받아야 합니다. 단, 메세지 전송, 색 코드 수정 등은 허가됩니다.\n\n";
		*/ //TODO: I will remove this message, or make shorter.
		$output .= "§l- 플러그인 체인지로그§r\n";
		$output .= $changelog . "\n";
		
		return $output;
	}
	
	public function getFormData() {
		$json = [];
		$json["type"] = "form";
		$json["title"] = "플러그인 정보보기";
		$json["content"] = $this->getText();
		$json["buttons"] = [];
		$json["buttons"][] = ["text" => "메뉴로 돌아가기"];
		
		return json_encode($json);
	}
	
}