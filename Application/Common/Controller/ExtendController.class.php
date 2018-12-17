<?php
/**模块公用控制器
 * @Author: happy
 * @Email:  976123967@qq.com
 * @Date:   2015-07-14 22:04:39
 * @Last Modified by:   cl
 * @Last Modified time: 2015-08-09 19:32:13
 */
namespace Common\Controller;
use Think\Controller;
use Think\Upload;
class ExtendController extends Controller{




	/**
   * [keditor_upload 编辑器图片上传]
   * @return [type] [description]
   */
  public function keditor_upload()
  {
      // 上传类
      $upload = new Upload();             // 实例化上传类
      $upload->maxSize  = 314572800 ;     // 设置附件上传大小
      $upload->exts  = explode('|', C('cfg_file'));// 设置附件上传类型
      $upload->autoSub =false;            //不要自动创建子目录
      $upload->rootPath = './Data/Uploads/'; //设置上传根路径 这个系统不会自动创建
      $dir = I('get.dir');
      $upload->savePath = $dir.'/'.date('Y').'/'.date('m').'/'.date('d').'/';


      // 执行上传
      if(!$info=$upload->uploadOne($_FILES['imgFile']))
      {
        // 上传错误提示错误信息
        echo json_encode(array('error' => 1, 'message' =>$upload->getError()));
      }
      else
      {
      		// 上传成功 获取上传文件信息
      		$fileUrl = $upload->rootPath.$info['savepath'].$info['savename'];
      		$keditor = pathinfo($fileUrl);
          $name = $_FILES['imgFile']['name'];
      		// 保存数据到数据库
      		$data=array(
      		  'name'=>$keditor['basename'],
      		  'ext'=>$keditor['extension'],
      		  'path'=>$keditor['dirname'],
      		  'size'=>filesize($fileUrl),
      		  'addtime'=>time(),
      		  'user_uid'=>session('user_id'),
            'remark'=>$name,
            'type'=>'editor',
      		);
      		$id = D('Upload')->add($data);
      		$_SESSION['keditor'][]=$id;
      		$fullPath = __ROOT__ . '/' . $fileUrl;
      		echo json_encode(array('error' => 0, 'url' => $fullPath));
      }
      exit;
  }


 /**
 * [down 下载]
 * @return [type] [description]
 */
  public function down()
  {
    $id  = I('get.id');
    $data =  D('Upload')->find($id);
    if(!$data)
      $this->error('文件不存在');
    $file = $data['path'].'/'.$data['name']; //文件路径
    $fileName = $data['remark'];            //获得文件名

    /* $file=iconv("utf-8","gb2312",$file);
    $fileName = basename($file);//获得文件名*/

    header("Content-type:application/octet-stream");//二进制文件
    header("Content-Disposition:attachment;filename={$fileName}");//下载窗口中显示的文件名
    header("Accept-ranges:bytes");//文件尺寸单位
    header("Accept-length:".filesize($file));//文件大小
    readfile($file);//读出文件内容
  }


  public function verify()
  {

    $fontSize = I('get.fontSize',14);
    $config = array(
      'codeSet'=>'0123456789',
      'length'=>4,
      'fontSize'=>$fontSize,
      'fontttf'=>'5.ttf'
    );
    $Verify = new \Think\Verify($config);
    $Verify->entry();
  }




}