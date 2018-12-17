<?php
namespace Home\Controller;
use Think\Controller;
use API\Wechat\WechatOpenAPI;
/*
请求数据格式 ：{
					"roomNum":"6-420",
					"equipment":["123456","125654"]
				}
*/
class EquipmentController extends Controller{
	
	public $roomModel;
	public $equipmentModel;
	
	public function _initialize()
	{
		$this->roomModel = M("room");
		$this->equipmentModel = M("equipment");
	}
	public function index(){	
		$resData = array(
			"code"=>"000",
			"message"=>'unoccupied!',
		);
		
		$json_string = file_get_contents("php://input");
		if(IS_POST && !empty($json_string)){	
			$obj=json_decode($json_string);
			
			$roomNum = $obj->roomNum;
			if(!empty($roomNum))
			{
				$roomDate = $this->roomModel->where("roomNum='{$roomNum}'")->find();
				if($roomDate["checkequipment"]=="start"){
					$resData["code"] = "3000";
					$resData["message"] = "check equipment!";
					$this->roomModel->checkequipment = 'run';
					$this->roomModel->where("roomNum='{$roomNum}'")->save();
				}
			}
			else{
				$resData["code"] = "4002";
				$resData["message"] = "empty roomNum!";
			}
			
			$equipment = $obj->equipment;
			if(!empty($equipment)){
				$this->roomModel->checkequipment = 'end';
				$this->roomModel->where("roomNum='{$roomNum}'")->save();
				foreach($equipment as $value){
					$data['blemac'] = $value;
					$data['state'] = 1;
					$this->equipmentModel->save($data);
					//if( !$this->equipmentModel->save($data) ){  //mac不存在
					//	$this->equipmentModel->create($data);
					//	$this->equipmentModel->add();
					//}
				}
			}
		}
		
		echo json_encode($resData);
	}
	
	public function equipData(){	
		$resData = array(
			"code"=>"000",
			"message"=>'unoccupied!',
		);
		
		$json_string = file_get_contents("php://input");
		if(IS_POST && !empty($json_string)){	
			$obj=json_decode($json_string);
			
			$roomNum = $obj->roomNum;
			if(!empty($roomNum))
			{
				$equipment = $obj->equipment;
				if(!empty($equipment)){
					$this->roomModel->checkequipment = 'end';
					$this->roomModel->where("roomNum='{$roomNum}'")->save();
					foreach($equipment as $value){
						$data['blemac'] = $value;
						$data['state'] = 1;
						$this->equipmentModel->save($data);
					}
					
					$equipment = $this->equipmentModel->order('id')->select();
					foreach($equipment as $value){
						if($value['state'] == 0){
							$losenEquip[]= $value['blemac'];
						}						
					}
					if(!empty($losenEquip)){
						$WechatOpen = new \API\Wechat\WechatOpenAPI();
						$WechatOpen->sendTemplate($losenEquip);
					}
			
				}
				else{
				$resData["code"] = "4004";
				$resData["message"] = "empty equipment!";
				}
			}
			else{
				$resData["code"] = "4002";
				$resData["message"] = "empty roomNum!";
			}
		}
		
		echo json_encode($resData);
	}
}