<?php
/**登录控制器
 * @Author: cl
 * @Date:   2015-07-24 23:25:53
 * @Last Modified by:   Administrator
 * @Last Modified time: 2015-10-29 13:12:28
 */

namespace Admin\Controller;
use Common\Controller\ExtendController;
//use Think\Controller;
class LoginController extends ExtendController{



	public $model;
	/**
	 * [_initialize 初始化]
	 * @return [type] [description]
	 */
	public function _initialize()
	{
		$this->model = D('User');
	}

	public function index()
	{

		if(IS_AJAX)
		{
			if(!$user = $this->model->validate_login())
			{

				if($_SESSION['error_num']>=3)
					$this->ajaxReturn(array('status'=>0,'info'=>$this->model->getError(),'show_code'=>1));
				else
					$this->ajaxReturn(array('status'=>0,'info'=>$this->model->getError(),'show_code'=>0));
			}
			else
			{
				$_SESSION['error_num']=0;
				if($user['role']!=1)
					$this->ajaxReturn(array('status'=>0,'info'=>'用户名或密码错误','show_code'=>0));
				// 更新登录信息
				$this->model->update_login($user);
				// 设置session
				$this->model->set_session($user);
				$this->ajaxReturn(array('status'=>1,'info'=>'','url'=>U('Index/index')));
			}
		}
		else
		{
		
			$this->display();
		}
		
	}

	/**
	 * [out 退出]
	 * @return [type] [description]
	 */
	public function out()
	{
		
		session('user_id',null);
		session('user_name',null);
		session('nick_name',null);
		session('group_name',null);
		
		$this->success('注销成功',U('Login/index'));
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