<?php
namespace Home\Controller;
use Think\Controller;
use API\Wechat\WechatOAuthAPI;
use API\Wechat\WechatOpenAPI;

class WechatController extends Controller {
    
    public $model;
    
    public function _initialize()
	{
		$this->model = D('Wechatuser');
	}
    
    public function index(){
        $wechat = new \API\Wechat\WechatAPI(C("WechatToken"), false);
		$wechat->run();
		//$wechat->onScan();
		//var_dump($this->model->verifldate_user('oc4Opt-RW_BzESFDS4HsmDoGktvI',"6-420"));
    }
    
    public function oauth_home(){
        
		if(IS_GET){
            $WechatOAuth = new \API\Wechat\WechatOAuthAPI();
            $openid = $WechatOAuth->getOpenID( I('get.code') );	
            //$openid = "34623652";
            if($openid)
            {
                session('openid',$openid);            
                $user = $this->model->get_one($openid);
                if(!$user)
                {
                    //$user = $this->model->creat_user($openid);  //创建新用户             
                    $this->redirect('user/sign_in');
                }
                else
				{
					if($user['auth'] == "未知用户"){
						$this->redirect('user/validing');
					}
					else {
						if($user["state"] == "结束")        //根据用户当前状态，进入不同方法
							$this->redirect('user/reLab');
						else  $this->redirect('user/myLab');
					}
				}
            }
            else{
                $this->error("微信授权错误");
            }
            
		}
		
	}
    public function oauth_admin(){
        
		if(IS_GET){
            $WechatOAuth = new \API\Wechat\WechatOAuthAPI();
            $openid = $WechatOAuth->getOpenID( I('get.code') );	
            //$openid = "oc4Opt-RW_BzESFDS4HsmDoGktvI";
            if($openid)
            {
                session('openid',$openid);
                $auth =  $this->model->getAuth($openid);
                if($auth == "金牌用户")
                {        
                    $this->redirect('manage/index');
                }
                else
                {
                    $this->error("你无权限访问！");
                }
            }
            else{
                $this->error("微信授权错误");
            }
            
		}
		
	}
	public function sendTemplate(){
		$WechatOpen = new \API\Wechat\WechatOpenAPI();
		$WechatOpen->sendTemplate();
		//$menu = $WechatOpen->menuCreate();
	}
}