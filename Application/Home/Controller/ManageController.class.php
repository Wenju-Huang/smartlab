<?php
namespace Home\Controller;
use Think\Controller;
use API\Wechat\WechatOAuthAPI;

class ManageController extends Controller {
    
    public $userModel;
	public $roomModel;
	public $cardModel;
    public $equipmentModel;
	
    public function _initialize()
	{
		$this->userModel = D('Wechatuser');
		$this->roomModel = M('room');
		$this->cardModel = M('carduser');
		$this->equipmentModel = M("equipment");
		$this->photoModel = M("photo");
	}
    
    public function index(){
        $openid = session('openid');  
		$auth =  $this->userModel->getAuth($openid);
		if($auth == "金牌用户")
		{ 
			$room = $this->roomModel->where("roomNum='6-420'")->find();			
			$equipment = $this->equipmentModel->order('id')->limit(5)->select();
			$maxuid = $this->photoModel->max('uid');
			$photo = $this->photoModel->where("uid=$maxuid")->find();
			$equipHTML = "<table class=bordered>
							<caption><h4> 仪器状况</h4></caption>
							<thead>
								<tr>
									<th>设备序号</th><th>Mac标签</th><th>设备状态</th>
								</tr>
							</thead>";
			foreach($equipment as $row)
			{
				 $equipHTML = $equipHTML."<tr>
				 <td>" . $row['id'] . "</td>
				 <td>" . $row['blemac'] . "</td>
				 <td>" . $row['state'] . "</td>
				</tr>";
			}
			$equipHTML = $equipHTML."</table>";
			$this->assign('tempture',22);
			$this->assign('humidity',63);
			$this->assign('equipHTML',$equipHTML);
			$this->assign('photo_url',$photo["photo_url"]);
			$this->display();
		}
        
    }
	public function equipDisplay(){
		$openid = session('openid');  
		$auth =  $this->userModel->getAuth($openid);
		if($auth == "金牌用户")
		{ 
			$room = $this->roomModel->where("roomNum='6-420'")->find();			
			$equipment = $this->equipmentModel->order('id')->select();
			$photo = $this->photoModel->select();
			$userList = $this->userModel->get_all();
			
			$this->assign('tempture',22);
			$this->assign('humidity',63);
			$this->assign('equipList',$equipment);
			$this->display("equipment");
		}
	}
	public function photoDisplay(){
		$openid = session('openid');  
		$auth =  $this->userModel->getAuth($openid);
		if($auth == "金牌用户")
		{ 	
			$photo = $this->photoModel->order('uid desc')->select();
			$this->assign('imageList',$photo);
			$this->display("photo");
		}
	}
	public function userDisplay(){
		$openid = session('openid');  
		$auth =  $this->userModel->getAuth($openid);
		if($auth == "金牌用户")
		{ 	
			if(IS_GET)
			{
				$userList = $this->userModel->order('auth')->select();
				$cardList = $this->cardModel->order('oauth')->select();
				$this->assign('userList',$userList);
				$this->assign('cardList',$cardList);
				$this->display("user");
			}
			if(IS_POST){
				$user_id = I('post.openid');
				$user_auth = I('post.auth');
				$user_kind = I('post.kind');
				if($user_kind == "wechat"){
					if($user_auth == "delete"){
						$this->userModel->delete_user($user_id);
					}
					else $this->userModel->updateAuth($user_id,$user_auth);
				}
				else if($user_kind == "card"){
					//if($user_auth == "delete"){
					//	$this->cardModel->where(array('cardNum'=>$user_id))->delete();
					//}
					//else 
					$this->cardModel->where(array('cardNum'=>$user_id))->setField('oauth',$user_auth);
				}
			}
		}
		
	}
	
	public function takePhoto(){
		$val = 0;
		//$imageUrl = $this->roomModel->where("roomNum='6-420'")->getField('photo');	
		$maxuid = $this->photoModel->max('uid');
		$photo = $this->photoModel->where("uid=$maxuid")->find();
		session('phototime',$photo["time"]);
		$this->roomModel->takephoto = 1;
		$this->roomModel->newphoto = 0;
		$this->roomModel->where("roomNum='6-420'")->save();
	}
	
	public function getPhoto(){

			 //若得到数据则马上返回数据给客服端，并结束本次请求  
			/*$newphoto = $this->roomModel->where("roomNum='6-420'")->getField('newphoto');
			if($newphoto){
				$imageUrl = $this->roomModel->where("roomNum='6-420'")->getField('photo');
				$this->roomModel->newphoto = 0;
				$this->roomModel->where("roomNum='6-420'")->save();
				$arr=array('success'=>"1",'imageUrl'=>"".C("ImagePath").$imageUrl);  
				echo json_encode($arr);  
				exit();  
			}else{
				$arr=array('success'=>"0",'imageUrl'=>"".C("ImagePath").$imageUrl);  

				echo json_encode($arr);  
			}*/
			$maxuid = $this->photoModel->max('uid');
			$photo = $this->photoModel->where("uid=$maxuid")->find();
			if($photo["time"] > session('phototime')){
				$arr=array('success'=>"1",'imageUrl'=>$photo['photo_url']);  
				echo json_encode($arr);  
				exit();  
			}
			/*if($newphoto){
				$imageUrl = $this->roomModel->where("roomNum='6-420'")->getField('photo');
				$this->roomModel->newphoto = 0;
				$this->roomModel->where("roomNum='6-420'")->save();
				$arr=array('success'=>"1",'imageUrl'=>"".C("ImagePath").$imageUrl);  
				echo json_encode($arr);  
				exit();  
			}*/
			else{
				$arr=array('success'=>"0",'imageUrl'=>"".C("ImagePath").$imageUrl);  

				echo json_encode($arr);  
			}
	}
	
	public function checkEquipment(){		
		$blemac = $this->equipmentModel->getField('blemac',true);
		//状态清零
		foreach($blemac as $value){
			$data['blemac'] = $value;
			$data['state'] = 0;
			$this->equipmentModel->save($data);
		}
		$this->roomModel->checkequipment = "start";
		$this->roomModel->where("roomNum='6-420'")->save();		
	}
	
	public function getEquipment(){
			
		$equipment = $this->equipmentModel->order('id')->select();
		//var_dump($equipment);
		$equipHTML = "<table class=bordered>
							<caption><h4> 仪器状况</h4></caption>
							<thead>
								<tr>
									<th>设备序号</th><th>Mac标签</th><th>设备状态</th>
								</tr>
							</thead>";
		foreach($equipment as $row)
		{
			 $equipHTML = $equipHTML."<tr>
			 <td>" . $row['id'] . "</td>
			 <td>" . $row['blemac'] . "</td>
			 <td>" . $row['state'] . "</td>
			</tr>";
		}
		$equipHTML = $equipHTML."</table>";
		
		if($this->roomModel->where("roomNum='6-420'")->getField('checkequipment') == "end")
			$arr=array('success'=>"1",'equipHTML'=>"".$equipHTML); 
		else $arr=array('success'=>"0",'equipHTML'=>"".$equipHTML);
		
		echo json_encode($arr);
	}
	public function getData(){
		if(empty($_POST['time']))exit();  
		set_time_limit(0);//无限请求超时时间  
		$i=0;  
		//$PreImageUrl = $this->roomModel->where("roomNum='6-420'")->getField('prephoto');
		//$PreImageUrl = session('PreImageUrl');
		while (true){  
			sleep(1);  
			$i++;  
			 //若得到数据则马上返回数据给客服端，并结束本次请求  
			$newphoto = $this->roomModel->where("roomNum='6-420'")->getField('newphoto');
			if($newphoto){
				$imageUrl = $this->roomModel->where("roomNum='6-420'")->getField('photo');
				$this->roomModel->newphoto = 0;
				$this->roomModel->where("roomNum='6-420'")->save();
				$arr=array('success'=>"1",'imageUrl'=>"".C("ImagePath").$imageUrl);  
				echo json_encode($arr);  
				exit();  
			}	  
			 //服务器($_POST['time'])秒后告诉客服端无数据  
			if($i==$_POST['time']){  
				$arr=array('success'=>"0",'imageUrl'=>"".C("ImagePath").$imageUrl);  
				echo json_encode($arr);  
				exit();  
			}  
		}
	}
	
}