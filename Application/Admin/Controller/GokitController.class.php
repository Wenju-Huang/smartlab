<?php
namespace Admin\Controller;  //命名空间，表示当前类所在的目录
use Think\Controller;

class GokitController extends Controller{      

    function index(){
        $this->updata();
        $gokitData = M("gokit_data")      //实例化数据库 gokit_data
            ->field("humidity, temperature")
            ->find( C('DID') );
        $this->assign("tempture",$gokitData["temperature"]);
        $this->assign("humidity",$gokitData["humidity"]);
        if($gokitData["led_r"])
            $this->assign("gray","red");
        else if($gokitData["led_g"])
            $this->assign("gray","green");
        else if ($gokitData["led_b"])
            $this->assign("gray","blue");
        else $this->assign("gray","gray");
         $this->display("index");   //默认与操作相同名的模板文件；
        
    }
    function control(){
        if(IS_GET)
        {
            $rgb = I('get.b',0);
            $gokitAPI =new \API\Gokit\GokitOpenAPI();
            switch($rgb){     
                case"red":  
                    $gokitAPI->control("LED_R",200);
                	$gokitAPI->control("LED_G",0);
                	$gokitAPI->control("LED_B",0);
                	break;
                case"green":  
                    $gokitAPI->control("LED_R",0);
                	$gokitAPI->control("LED_G",200);
                	$gokitAPI->control("LED_B",0);
                	break;
                case"blue":  
                	
                	$gokitAPI->control("LED_B",200);
                    $gokitAPI->control("LED_R",0);
                	$gokitAPI->control("LED_G",0);
                break;
                case"close":  
                    $gokitAPI->control("LED_R",0);
                	$gokitAPI->control("LED_G",0);
                	$gokitAPI->control("LED_B",0);
                break;
            }  
            
        }else{};
    }
    function updata(){
        $gokitAPI =new \API\Gokit\GokitOpenAPI();
        $gokit_model = M("gokit_data"); 
        $gokit_data = $gokitAPI->devdata();
        $gokit_attr = $gokit_data["attr"];
        $gokit_attr["did"] = $gokit_data["did"];
        $gokit_model->save($gokit_attr);
        //else $gokit_model->add($gokit_attr);
    }
    function test(){
        A('Gokit','Service')->JsLogin(); 
        //echo'非法请求';
    }
        
}
?>