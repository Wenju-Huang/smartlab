<?php

namespace Home\Model;
use Think\Model;

class WechatuserModel extends Model{
    
        public $username; //用户名
        public $id ; //学号
		public $email ;
        public $starttime ;   //开始时间
        public $endtime ;   //结束时间
    
    public function getAuth($openid){
        $user = $this->where(array('openid'=>$openid))->find();
        if(!$user){
            //$user['openid'] = $openid;
            //$user["auth"] = "未知用户";
            $auth = "未知用户";
        }
        else 
        {
            $auth = $user["auth"];
        }
        return $auth;
    }
	
	public function updateAuth($openid,$auth){
        $user = $this->where(array('openid'=>$openid))->setField('auth',$auth);
    }
        
     /**
     * [get_one 读取一条记录]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_one($openid)
    {
      $data = $this->where(array('openid'=>$openid))->find();
      if(I('get.role')==1)
      {
        $access = D('AuthGroupAccess')->where(array('openid'=>$uid))->getField('group_id',true);
        $data['access'] =  $access;
      
        return $data;
      }
    
    	return $data;
    }
    
	/**
     * [get_one 读取所有记录]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_all($openid)
    {
		$data = $this->select();
		return $data;
    }
	/**
       * [validate_login 验证登录]
       * @return [type] [description]
       */
    public function validate_sign()
    {
        /******接收数据******/
        $this->name = I('post.username'); //用户名
        $this->id = I('post.id'); //学号
        $this->email = I('post.email');   //开始时间
       
        /******普通验证******/
        
        // 用户名不为空
        if(!$this->name||$this->name=='请输入用户名')
        {
            $this->error = '请输入姓名';
            return false;
        }
        // 密码不为空
        if(!$this->id)
        {
            $this->error = '请输入学号';
            return false;
        }
        if(!$this->email)
        {
            $this->error = '请输入邮箱';
            return false;
        }
        
        return true;
    }
    /**
       * [validate_login 验证登录]
       * @return [type] [description]
       */
    public function validate_login()
    {
        /******接收数据******/
        //$this->name = I('post.username'); //用户名
        //$this->id = I('post.id'); //学号
        $this->starttime = I('post.starttime');   //开始时间
        $this->endtime = I('post.endtime');   //结束时间
        $this->laboratory = I('post.laboratory');
        /******普通验证******/
        
        if(!$this->starttime)
        {
            $this->error = '请输入开始时间';
            return false;
        }
        if(!$this->endtime)
        {
            $this->error = '请输入结束时间';
            return false;
        }
		
		if(!$this->laboratory)
        {
            $this->error = '请选择实验室';
            return false;
        }
        
        if(!isset($_SESSION['error_num']))
            $_SESSION['error_num']=0;
        
        return true;
    }
    
        /**
       * [update_login 更新登录信息]
       * @param  [type] $user [description]
       * @return [type]       [description]
       */
    public function update_login($openid)
    {
        // 更新登录信息
        $data = array(
            //'login_time'=> time(),
            //'times'   => $user['times']+1,
            'openid' => $openid,
            //'username' => $this->name,
            //'s_num'  => $this->id ,
            'laboratory' => $this->laboratory,
            'state'  => "预约",
            'starttime'=> $this->starttime,
            'endtime'=> $this->endtime,
        );
        $this->save($data);
        //$this->save($data);
    }
    
    /**
       * [update_login 添加用户]
       * @param  [type] $user [description]
       * @return [type]       [description]
       */
    public function creat_user($openid)
    {
        // 更新登录信息
        $data = array(
            'openid' => $openid,
			'username' => $this->name,
            's_num'  => $this->id ,
            'state'  => "结束",
            'auth'  => "未知用户",
        );
        
        $this->create($data);
        $this->add();
    }
	
	public function delete_user($openid)
    { 
        $this->where(array('openid'=>$openid))->delete();
    }
    
    public function update_state($openid)
    {
        // 更新登录信息
        $data = array(
            //'login_time'=> time(),
            //'times'   => $user['times']+1,
            'openid' => $openid,
            'state'  => "结束",
            'starttime'=> NULL,
            'endtime'=> NULL
        );
        
        $this->save($data);
    }
    
	public function verifldate_user($openid,$lab)
    {
		$user = $this->get_one($openid);
		$room = M("room");
		$doorModel = M("door");
		if(!empty($user)){
			if($user['auth']=="高级用户" || $user['auth']=="金牌用户")
			{
				$room->doorState = 1;
				$room->where("roomNum='{$lab}'")->save();
				
				$doorModel->roomNum = $lab;
				$doorModel->create_time = date("Y-m-d H:i:s");
				$doorModel->type = "wechat";
				$doorModel->user_id = $openid;
				$doorModel->username = $user['username'];
				$doorModel->doorState = "open";
				$doorModel->add();
				
				return "欢迎".$user['auth'].$user['username']."，请自觉遵守实验室守则，祝你使用愉快O(∩_∩)O~~";
			}
			
			if(empty($user['auth']) || $user['auth']=="未知用户")
				return "请先预约再使用，如有问题，请联系管理员赵焕城，电话：110";
			if($user['auth']=="一般用户" && $user['state']=="结束")
				return "抱歉，您的预约已结束，如需继续使用，请重新预定，谢谢合作!";
			if($user['laboratory'] != $lab)
				return "抱歉，您没有此间实验室的使用权限！";
			
			$starttime = strtotime($user['starttime']);
			$endtime = strtotime($user['endtime']);
			$nowtime = strtotime('now');

			if(!$starttime || !$endtime)
				return "抱歉，您的预约时间不符合要求，请重新预约！";
			if($nowtime < $starttime || $nowtime > $endtime)
				return "抱歉，您的预约时间还没开始，或者已经结束，请等待或者重新预约！";
			
			$room->doorState = 1;
			$room->where("roomNum='{$lab}'")->save();
			return "欢迎".$user['auth'].$user['username']."，请自觉遵守实验室守则，祝你使用愉快O(∩_∩)O~~";
		}
		else{
			return "用户不存在";
		}
    }
    /**
   * [set_session set_session]
   * @param [type] $user [description]
   */
    public function set_session($user)
    {
        session('user_id',$user['s_num']);
        session('user_name',$user['name']);
        
    }
}