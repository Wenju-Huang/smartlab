<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {
    
    public $model;
    
    public function _initialize()
	{
		$this->model = D('Wechatuser');
	}
    
    public function index(){
        $openid = session('openid');
        $user = $this->model->get_one($openid);
        if(!$user)
        {
            //$user = $this->model->creat_user($openid);  //创建新用户             
            $this->redirect('sign_in');
        }
        else
        {
			if($user['auth'] == "未知用户"){
				$this->redirect('validing');
			}
			else {
				if($user["state"] == "结束")        //根据用户当前状态，进入不同方法
					$this->redirect('reLab');
				else  $this->redirect('myLab');
			}
        }
    }
	public function sign_in(){
        
        $openid = session('openid');      
        if(IS_AJAX)
		{
            if(!$this->model->validate_sign())
			{

				if($_SESSION['error_num']>=3)
					$this->ajaxReturn(array('status'=>0,'info'=>$this->model->getError(),'show_code'=>1));
				else
					$this->ajaxReturn(array('status'=>0,'info'=>$this->model->getError(),'show_code'=>0));
			}
            else
            {
				$this->model->creat_user($openid);
				$this->ajaxReturn(array('status'=>1,'info'=>'','url'=>U('validing')));
            }
		}
		else
		{
		
            $this->display("sign_in");
		}
    }
    public function reLab(){
        
        $openid = session('openid'); 
		$user = $this->model->get_one($openid);
		if(!$user)
        {      
            $this->redirect('sign_in');
        }
		else if($user['auth'] == "未知用户"){
				$this->redirect('validing');
		}else{
			if(IS_AJAX)
			{
				if(!$this->model->validate_login())
				{

					if($_SESSION['error_num']>=3)
						$this->ajaxReturn(array('status'=>0,'info'=>$this->model->getError(),'show_code'=>1));
					else
						$this->ajaxReturn(array('status'=>0,'info'=>$this->model->getError(),'show_code'=>0));
				}
				else
				{
					$this->model->update_login($openid);
					$this->ajaxReturn(array('status'=>1,'info'=>'','url'=>U('myLab')));
				}
			}
			else
			{
				$this->assign('username',$user["username"]);
				$this->assign('auth',$user["auth"]);
				$this->display("reLab");
			}
		}
    }
    
    public function myLab(){
        $openid = session('openid');
        $user = $this->model->get_one($openid);
		if(!$user)
        {         
            $this->redirect('sign_in');
        }
		else if($user['auth'] == "未知用户"){
				$this->redirect('validing');
		}
		else 
        {
            $this->assign('username',$user["username"]);
            $this->assign('auth',$user["auth"]);
            $this->assign('laboratory',$user["laboratory"]);
            $this->assign('starttime',$user["starttime"]);
            $this->assign('endtime',$user["endtime"]);
			$this->display("myLab");
        }	
	}
	
	public function validing(){
        $openid = session('openid');
        $user = $this->model->get_one($openid);
		if(!$user)
        {         
            $this->redirect('sign_in');
        }
		else if($user['auth'] == "未知用户"){
			$this->assign('username',$user["username"]);
            $this->assign('s_num',$user["s_num"]);
			$this->display("validing");
		}
        else{
			$this->redirect('index');
		}
		
	}
	
	public function delete_one(){
        $this->model->delete_user(session('openid'));
		$this->success('注销成功',U('index'));
	}
    
    /**
	 * [out 退出]
	 * @return [type] [description]
	 */
	public function out()
	{
		$this->model->update_state(session('openid'));
		$this->success('注销成功',U('index'));
	}
    
    /**
	 * [ajax_show_code 是否显示验证码]
	 * @return [type] [description]
	 */
	public function ajax_show_code()
	{
		if($_SESSION['error_num']>=3)
			echo 1;
		else
			echo 0;
		die;
	}
}