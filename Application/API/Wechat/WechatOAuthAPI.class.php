<?php
namespace API\Wechat;  //命名空间，表示当前类所在的目录
use Org\Net\GokitHttp;

class WechatOAuthAPI {
    public function index(){
        
    }
	
	public function getOpenID($code){
		$head = "";
        $url ="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".C("WechatAppID")."&secret=".C("WechatAppsecret")."&code=".$code."&grant_type=authorization_code";
		$r = GokitHttp::get($head,$url);
		if(isset($r["openid"])){
			return $r["openid"];
		}
		else {
			return 0;
		}
		
	}
}
?>