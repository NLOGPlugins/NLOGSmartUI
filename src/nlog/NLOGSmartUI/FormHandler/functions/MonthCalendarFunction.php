<?php

namespace nlog\NLOGSmartUI\FormHandler\functions;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use nlog\NLOGSmartUI\FormHandler\MenuForm;

class MonthCalendarFunction extends FormFunction {
	
	public static function getName() {
		return "이번 달 달력";
	}
	
	public function sendPacket() {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->owner->MonthCalendarID;
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
	
	public function getInformation() {
		$output = "일  월  화  수  목  금  토";
		$output .= "\n§f";
		$s_Y = date("Y"); //연도 : year
		$s_m = date("m"); //달 : month
		
		$today = date("d");
		
		$s_n = date("N",mktime(0,0,0,$s_m,1,$s_Y)); //첫째날 요일
		
		# 1 => 월 ~ 7 => 일
		$s_t = date("t",mktime(0,0,0,$s_m,1,$s_Y)); //마지막날짜
		
		switch($s_n) {
			case 1:
				$output .= str_repeat("  ", 1);
				break;
			case 2:
				$output .= str_repeat("  ", 3);
				break;
			case 3:
				$output .= str_repeat("  ", 5);
				break;
			case 4:
				$output .= str_repeat("  ", 7);
				break;
			case 5:
				$output .= str_repeat("  ", 9);
				break;
			case 6:
				$output .= str_repeat("  ", 11);
				break;
		}
		
		$day = ++$s_n;
		
		for ($i = 1; $i <= $s_t; $i++) {
			if ($i < 10) {
				if ($i == date("d")) {
					$output .= " §a$i  §f";
				}elseif ($day === 7) {
					$output .= " §b$i  §f";
				}elseif ($day === 1){
					$output .= "§c$i  §f";
				}else{
					$output .= " $i  ";
				}
			}else{
				if ($i == date("d")) {
					$output .= "§a$i  §f";
				}elseif ($day === 7) {
					$output .= "§b$i  §f";
				}elseif ($day === 1){
					$output .= "§c$i  §f";
				}else{
					$output .= "$i  ";
				}
			}
			if (++$day === 8) {
				$output .= "\n";
				$day = 1;
			}
		}
		
		return $output;
	}
	
	
	public function getFormData() {
		$json = [];
		$json["type"] = "modal";
		$json["title"] = "이번 달 달력 보기";
		$json["content"] = $this->getInformation();
		$json["button1"] = "메뉴로 돌아가기"; //true
		$json["button2"] = "닫기"; //false
		
		return json_encode($json);
	}
	
}