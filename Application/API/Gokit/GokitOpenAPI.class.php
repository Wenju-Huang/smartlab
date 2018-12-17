<?php
namespace API\Gokit;
use Org\Net\GokitHttp;

class GokitOpenAPI{
    public function user() //注册用户
    {

        // 参数数组
        $head = array (
                'appid' => C('APPID'),
        	);
        
        $data = array (
                'email' => C('EMAIL'),
                'password' => C('PASS_WARD'),
        	);
        $uri = C('GOKIT_URI')."/users";
        $r = GokitHttp::post($head,$data,$uri);
		var_dump($r);
     }
    
     public function login()  //用户登陆
    {
        // 参数数组
         $head = array (
                'appid' => C('APPID'),
        	);
        
         $data = array (
                'username' => C('EMAIL'),
                'password' => C('PASS_WARD'),
        	);
         $uri = C('GOKIT_URI')."/login";
         $r = GokitHttp::post($head,$data,$uri);
         var_dump($r);
     }
    
     public function devdata()  //获取最近数据点
    {
         $head = array (
                'appid' => C('APPID'),
        	);
         $uri = C('GOKIT_URI')."/devdata/".C('DID')."/latest";
		 $r = GokitHttp::get($head,$uri);
         //var_dump($r);  //输出 $r
         return $r;
     }
    
     public static function bindings()  //绑定设备
    {

         $head = array (
             'appid' => C('APPID'),
             'token' => C('TOKEN')
         );
         $data = array ( 
             'devices' => 
             array(
                 array(
                     'did' => C('DID'),
                     'passcode' =>  'LFPLWQRSNE',
                     'remark' => 'Library'
                 )
             )
         );
         $uri = C('GOKIT_URI').'/bindings';
         $r = GokitHttp::post($head,$data,$uri);  //post数据
         var_dump($r);  //输出 $r
     }
    
    function devices()  //获取设备详情信息
    {
        $head = array (
                'appid' => C('APPID'),
        	);
         $uri =  C('GOKIT_URI').'/devices/'.C('DID');
         $r = GokitHttp::get($head,$uri);
         var_dump($r);  //输出 $r
     }
    
    public function devices2()  //根据product_key和mac查询设备 获取 passcode
    {
        $head = array (
                'appid' => C('APPID'),
        	);
        $uri = C('GOKIT_URI').'/devices?product_key=6f3074fe43894547a4f1314bd7e3ae0b&mac=accf2345442c';
		$r = GokitHttp::get($head,$uri);
		var_dump($r);  //输出 $r
     }
    
     public static function control($attr, $val)  //控制设备
    {
         // 参数数组
         $head = array (
             'appid' => C('APPID'),
             'token' => C('TOKEN')
         );
         $data = array (        
             'attr' => $attr,
             'val' => $val,
         );        
         $uri = C('GOKIT_URI').'/control/'.C('DID');
         $r = GokitHttp::post($head,$data,$uri);  //post数据
         //var_dump($r);  //输出 $r
     }
}

?>