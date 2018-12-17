<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
		//$s = new Storage("labscnu:y5mmxjklno", "4m1ml042yxl003w3z4014j1zwm3wm0i0i1yh2yjk");
		//$s->putObjectFile($_FILES['fieldNameHere']['tmp_name'], "image", "1.txt");
	}
	public function test(){
		
	}
	public function upload(){
		if(IS_POST)
		{
			$upload = new \Think\Upload();//  实例化上传类
			$upload->maxSize = 3145728 ;//  设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');//  设置附件上传类型
			$upload->savePath = './Public/Uploads/'; //  设置附件上传目录
			$upload->saveName = "time";
			//  上传单个文件
			$info = $upload->uploadOne($_FILES['fieldNameHere']);
			if(!$info) {//  上传错误提示错误信息
				$this->error($upload->getError());
			}else{//  上传成功 获取上传文件信息
				echo $info['savepath'].$info['savename'];
				echo C('/Public/upload');
			}
		}
 }
}