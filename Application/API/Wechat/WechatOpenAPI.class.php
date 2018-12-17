<?php 
namespace API\Wechat;  //命名空间，表示当前类所在的目录
use Org\Net\GokitHttp;

class WechatOpenAPI
{
    public static function get_token() //生成token
    {
        $uri = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".C("WechatAppID")."&secret=".C("WechatAppsecret");
        $apistr = file_get_contents($uri);
        $api_data = json_decode($apistr);//解析json文件
        $wechat_token = $api_data->access_token;
        $mmc=memcache_init();
        if($mmc==false)
		{
			echo "mc init failed\n";
			return null;
		}           
        else
        {
            memcache_set($mmc,"key","$wechat_token");
			return $wechat_token;
        }
    }
    public static function create_qrcode()  //生成二维码
    {
        $mmc=memcache_init();
        if($mmc==false)
            echo "mc init failed\n";
        else
        {
           $wechat_token =  memcache_get($mmc,"key");
        }
        $data = array (
            "device_num"=>"1",
            "device_id_list"=>"wqeD95N9AagxuSp9K2cQqY",
        );
        $uri = "https://api.weixin.qq.com/device/create_qrcode?access_token={$wechat_token}";
		$r = Gokit_Http::post($uri,$data);
		var_dump($r);  //输出 $r
    }
     public static function auth_devices()  //设备授权
    {
        $mmc=memcache_init();
        if($mmc==false)
            echo "mc init failed\n";
        else
        {
           $wechat_token =  memcache_get($mmc,"key");
        }
 
        $data = array (
            "device_num" =>"1",
   			 "device_list"=>
            		array(
                            array(
                                
                                "id" =>"gh_c4838a73c74b_db9b85cceba4da683a04615ba0aed2b8",
                                "mac"=>"F94582887E62",
                                "connect_protocol"=>"3|1",
                                "auth_key"=>"",
                                "close_strategy"=>"2",
                                "conn_strategy"=>"1",
                                "crypt_method"=>"0",
                                "auth_ver"=>"0",
                                "manu_mac_pos"=>"-1",
                                "ser_mac_pos"=>"-2"
                            )
                		),
           
            "op_type"=>"1"
        );
         $uri = "https://api.weixin.qq.com/device/authorize_device?access_token={$wechat_token}";
		$r = Gokit_Http::post($uri,$data);
		var_dump($r);  //输出 $r
    }
    public static function transmsg()  //第三方发送设备状态消息给设备主人的微信终端。
    {
        $mmc=memcache_init();
        if($mmc==false)
            echo "mc init failed\n";
        else
        {
           $wechat_token =  memcache_get($mmc,"key");
        }
 
        $data = array (
                "device_type"=> "gh_c4838a73c74b", 
                "device_id"=> "wqeD95N9AagxuSp9K2cQqY", 
                "open_id"=>"oc4Opt-RW_BzESFDS4HsmDoGktvI", 
                "msg_type"=> "2", 
                "device_status"=> "1"
        );
		$uri = "https://api.weixin.qq.com/device/transmsg?access_token={$wechat_token}";
		$r = Gokit_Http::post($uri,$data);
		var_dump($r);  //输出 $r
    }
    
     public static function shakearound_register()  //第三方发送设备状态消息给设备主人的微信终端。
    {
        $mmc=memcache_init();
        if($mmc==false)
            echo "mc init failed\n";
        else
        {
           $wechat_token =  memcache_get($mmc,"key");
        }
 
        $data = array (
                "name"=> "黄文举", 
                "phone_number"=> "15603004734", 
                "email"=>"1023423901@qq.com", 
                "industry_id"=> "1401", 
            	"qualification_cert_urls"=> " ",
                "apply_reason"=> "测试摇一摇事件推送功能，完成智能硬件开发"
        );
		$uri = "https://api.weixin.qq.com/shakearound/account/register?access_token={$wechat_token}";
		$r = Gokit_Http::post($uri,$data);
		//var_dump($r);  //输出 $r
    }
	
	public static function menuCreate() //创建自定义菜单,由编码问题，目前只能设置英文键值
    {
        $mmc=memcache_init();
        if($mmc==false)
            echo "mc init failed\n";
        else
        {
           $wechat_token =  memcache_get($mmc,"key");
        }
        $data = array (
			"button"=>
			array(
					array(
							"type"=>"scancode_waitmsg", 
							"name"=> "开门", 
							"key"=>"OpenDoor"
					), 
					array(
							"type"=>"click", 
							"name"=>"菜单", 
							"key"=>"yuyue" 
					)
			)
		);
        $uri = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$wechat_token}";
		$r = GokitHttp::post("",$data,$uri);
		return $r;
    }
	
	public static function sendTemplate($losenEquip){
		$uri = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".C("WechatAppID")."&secret=".C("WechatAppsecret");
        $apistr = file_get_contents($uri);
        $api_data = json_decode($apistr);//解析json文件
        $wechat_token = $api_data->access_token;
		$equipNum = count($losenEquip);
		$equipMac = "";
		foreach($losenEquip as $value){
			$equipMac = $equipMac.$value."\n";
		}
		$data = array (
			"touser"=>"oc4Opt-RW_BzESFDS4HsmDoGktvI",
			"template_id"=>"HLFwVMff-LfrIuUJkHQCk_GgM93OunvNWfZV6q6YsrQ",
			"url"=>"http://weixin.qq.com/download",            
			"data"=>array(
				"lab"=>array(
					"value"=>"理六420",
					"color"=>"#173177"
				),
				"num"=>array(
					"value"=>$equipNum,
					"color"=>"#173177"
				),
				"mac"=>array(
					"value"=>$equipMac,
					"color"=>"#173177"
				),
			)
		);
		$uri = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$wechat_token}";
		$r = GokitHttp::post("",$data,$uri);
		//var_dump($r);
	}
	
}
?>