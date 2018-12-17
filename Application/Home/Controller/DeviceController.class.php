<?php
namespace Home\Controller;
use Think\Controller;
class DeviceController extends Controller{
	
	public function index(){
		
		$resData = array(
			"code"=>"0000",
			"message"=>'unoccupied!'
		);
		$roomModel = M("room");
		$userModel = M("carduser");
		$doorModel = M("door");
		$json_string = file_get_contents("php://input");
		
		if(IS_POST && !empty($json_string)){	
			$obj=json_decode($json_string);
			$roomNum = $obj->roomNum;
			$cardNum = $obj->cardNum;
			$newCard = $obj->newCard;
			if(!empty($roomNum))
			{
				if(!empty($cardNum)){
					
					$userData = $userModel->where("cardNum={$cardNum}")->find();					
					if(empty($userData))
					{
						if($cardNum != "00000000"){
							$doorModel->roomNum = $roomNum;
							$doorModel->create_time = date("Y-m-d H:i:s");
							$doorModel->type = "card";
							$doorModel->user_id = $cardNum;
							$doorModel->doorState = "invalid IDCard!";
							$doorModel->add(); 
							
							$userModel->cardNum = $cardNum;
							$userModel->oauth = "未授权";
							$userModel->add(); 
							
							$resData["code"] = "1002";
							$resData["message"] = "invalid IDCard!";
							$resData["cardNum"] = $cardNum;
						}					
						
					}
					else {
						if( $userData["oauth"]=="未授权" )
						{
							$doorModel->roomNum = $roomNum;
							$doorModel->create_time = date("Y-m-d H:i:s");
							$doorModel->type = "card";
							$doorModel->user_id = $cardNum;
							$doorModel->doorState = "invalid IDCard!";
							$doorModel->add(); 
							
							$resData["code"] = "1002";
							$resData["message"] = "invalid IDCard!";
							$resData["cardNum"] = $cardNum;
						}					
			
						else if($userData["oauth"]=="已授权"){
							if($cardNum == "00000001"){
								$doorModel->type = "key";
							}
							else {
								$doorModel->type = "card";
							}
							$doorModel->roomNum = $roomNum;
							$doorModel->create_time = date("Y-m-d H:i:s");
							$doorModel->user_id = $cardNum;
							$doorModel->username = $userData["name"];
							$doorModel->doorState = "open";
							$doorModel->add();
							if($newCard == 1){
								$resData["code"] = "1008";
								$resData["message"] = "open door!";
								$resData["cardNum"] = $cardNum;
							}
							else{
								$resData["code"] = "1004";
								$resData["message"] = "record";
							}
							
						}
					}
				}
				
				$lab = $roomModel->where("roomNum='{$roomNum}'")->find();
				if(empty($lab))
				{
					$resData["code"] = "4004";
					$resData["message"] = "error roomNum!";
				}
				if($lab["doorstate"])
				{
					$resData["code"] = "1000";
					$resData["message"] = "open door!";
					$roomModel->doorState = 0;
					$roomModel->where("roomNum='{$roomNum}'")->save();
				}
			}
			else{
				$resData["code"] = "4002";
				$resData["message"] = "empty roomNum!";
			}
			
		}
		else{
			
			$resData["code"] = "4000";
			$resData["message"] = "empty post data!";
		}
		if($resData["code"] == "0000"){
			$userData = $userModel->where("oauth='待删除'")->getField('cardnum',true);
				if(!empty($userData)){
					$resData = array(
						"code"=>"1006",
						"message"=>'delect user!',
						'cardnum' => $userData
					);
					$userModel->where("oauth='待删除'")->delete();
				}
		}
		
		/*if($resData["code"] == "0000"){   
			while (true){  
				sleep(1);  
				$i++;  
				 //若得到数据则马上返回数据给客服端，并结束本次请求  
				$lab = $roomModel->where("roomNum='{$roomNum}'")->find();
				if($lab["doorstate"])
				{
					$resData["code"] = "1000";
					$resData["message"] = "open door!";
					$roomModel->doorState = 0;
					$roomModel->where("roomNum='{$roomNum}'")->save();
					echo json_encode($resData);
					exit();
				}
				 //服务器($_POST['time'])秒后告诉客服端无数据  或者是否更新卡
				if($i==3){ 
					$userData = $userModel->where("oauth='待删除'")->getField('cardnum',true);
					if(!empty($userData)){
						$resData = array(
							"code"=>"1006",
							"message"=>'delect user!',
							'cardnum' => $userData
						);
						$userModel->where("oauth='待删除'")->delete();
					}
					echo json_encode($resData); 
					exit();  
				}  
			}
		}*/
		echo json_encode($resData);
	}
}