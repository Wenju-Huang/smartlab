<?php
/**会员表模型
 * @Author: 976123967@qq.com
 * @Date:   2015-07-23 10:12:17
 * @Last Modified by:   Administrator
 * @Last Modified time: 2015-09-18 14:44:21
 */
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
	// 自动验证
    /* array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
	*
	*  验证条件
	*  Model::EXISTS_VALIDATE 或者0 存在字段就验证 （默认）
	*  Model::MUST_VALIDATE 或者1 必须验证
	*  Model::VALUE_VALIDATE或者2 值不为空的时候验证
	*
	*  验证时间
	*  Model:: MODEL_INSERT 或者1新增数据时候验证
	*  Model:: MODEL_UPDATE 或者2编辑数据时候验证
	*  Model:: MODEL_BOTH 或者3 全部情况下验证（默认）
	*/

    protected $_validate=array(
    	
      // 用户名验证
      array('username','require','用户名必须填写',1,'regex',1),
     	array('username','check_username','用户名已经存在',1,'callback',3),
     	array('nickname','require','昵称必须填写',1,'regex',3),
     	array('email','require','邮箱必须填写',1,'regex',3),
     	array('email','email','邮箱格式不对',1,'regex',3),

     	array('password','require','密码必须填写',1,'regex',1),
     	array('password','/^.{6,}$/','密码长度至少6位',1,'regex',1),
     	array('passwords','require','确认密码必须填写',1,'regex',1),
     	array('passwords','password','确认密码不正确',1,'confirm',1), 


   		// 编辑时候验证
     	array('password','/^.{6,}$/','密码长度至少6位',2,'regex',2),
     	array('passwords','check_edit_passwords','确认密码必须填写',1,'callback',2),
     	array('passwords','password','确认密码不正确',1,'confirm',2), 
    );




    /**
     * [check_edit_passwords 编辑时候修改密码验证]
     * @return [type] [description]
     */
    public function check_edit_passwords($con)
    {

    	$password = I('post.password');
    	if($password != '' && $con =='')
    		return false;
    	else
    		return true;
    }




    /**
	 * [check_username 验证用户名称是否重复]
	 * @param  [type] $con [description]
	 * @return [type]      [description]
	 */
	public function check_username($con)
	{
		$where['username'] = $con;
		$uid = I('post.uid');
		if($uid)
		{
			$where['uid'] = array('neq',$uid);
		}
		if($this->where($where)->getField('uid'))
		{
			return false;
		}
		else
			return true;
	}


	// 自动完成
    protected $_auto = array (

        // 时间
        array('addtime','time',1,'function'), 
        //array('login_time','time',1,'function'), 
        //ip
        array('login_ip','_ip',1,'callback'),
        // 密码
        array('password','md5',3,'function'),
      
    );

    // ip
    public function _ip()
    {
    	return get_client_ip();
    }

    /**
     * [get_one 读取一条记录]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_one($uid)
    {
      $data = $this->find($uid);
      if(I('get.role')==1)
      {
        $access = D('AuthGroupAccess')->where(array('uid'=>$uid))->getField('group_id',true);
        $data['access'] =  $access;
      
        return $data;
      }
    
    	return $data;
    }

    /**
     * [del 删除]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
   	public function del($uids)
   	{
      $uids = explode(',', $uids);
   		$db  = D('UserBaseinfo');
      foreach($uids as $uid)
      {
        if($uid==1)
          continue;
        $face = $db->where(array('user_uid'=>$uid))->getField('face');
        is_file($face) && unlink($face);
        $db->where(array('user_uid'=>$uid))->delete();
        $this->delete($uid);
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
      $username = I('post.username'); //用户名
      $id = I('post.id'); //密码
      $code     = I('post.code');   //验证码

      /******普通验证******/
      
      
      // 用户名不为空
      if(!$username||$username=='请输入用户名')
      {
        $this->error = '请输入用户名';
        return false;
      }
      // 密码不为空
      if(!$id)
      {
        $this->error = '请输入学号';
        return false;
      }

      if(!isset($_SESSION['error_num']))
          $_SESSION['error_num']=0;
      // 验证码验不为空
      /*if($_SESSION['error_num']>=3 && !$code)
      {
        $this->error = '请输入验证码';
        return false;
      }
      $verify = new \Think\Verify();
      // 验证码是否相等
      if($_SESSION['error_num']>=3 && !$verify->check($code))
      {
        $this->error = '验证码输入错误';
        return false;
      } */
      /******数据库验证******/
      $user   = $this->where(array('username'=>$username))->find();
      // 用户不存在
      if(!$user)
      {
        $this->error = '用户名或密码错误';
        $_SESSION['error_num']++;
        return false;
      }
      // 密码不对
      if($user['password'] != md5($password))
      {
        $this->error = '用户名或密码错误';
        $_SESSION['error_num']++;
        return false;
      }

      if($user['is_lock'])
      {
        $this->error = '用户锁定中，请联系管理员';
        return false;
      }

      $auto = I('post.auto');
      if($auto)
        setcookie(session_name(),session_id(),time()+60*60*24*14,'/');
      else
        setcookie(session_name(),session_id(),0,'/');

      return $user;
    } 

   /**
   * [update_login 更新登录信息]
   * @param  [type] $user [description]
   * @return [type]       [description]
   */
  public function update_login($user)
  {
    // 更新登录信息
    $data = array(
      'login_time'=> time(),
      'times'   => $user['times']+1,
      'login_ip'  => get_client_ip(),
      'uid'   =>$user['uid'],
    );
    $this->save($data);
  }

  /**
   * [set_session set_session]
   * @param [type] $user [description]
   */
  public function set_session($user)
  {
    session('user_id',$user['uid']);
    session('user_name',$user['username']);
    session('nick_name',$user['nickname']);

  }


  /**
   * [update_cur 修改用户]
   * @return [type] [description]
   */
  public function update_cur()
  {
    $email     = I('post.email');
    $nickname  = I('post.nickname');

    if(!$email)
    {
      $this->error='请输入邮箱';
      return false;
    }
    if(!$nickname)
    {
      $this->error='请输入昵称';
      return false;
    }
    $uid = session('user_id');
    $this->save(array('email'=>$email,'nickname'=>$nickname,'uid'=>$uid));

    session('nick_name',$nickname);

    return true;
  }


  /**
   * [update_change 修改密码]
   * @return [type] [description]
   */
  public function update_change()
  {
    // 新密码验证
    $password = I('post.password');
    if(!$password)
    {
      $this->error='请输入新密码';
      return false;
    }

    if(strlen($password)<6)
    {
      $this->error='新密码长度至少6位';
      return false;
    }
    // 确认密码验证
    $passwords = I('post.passwords');
    if(!$passwords)
    {
      $this->error='请输入确认新密码';
      return false;
    }

    if($password!=$passwords)
    {
      $this->error='两次密码不一致';
      return false;
    }
    // 旧密码
    $oldpassword = I('post.oldpassword');
    if(!$oldpassword)
    {
      $this->error='请输入旧密码';
      return false;
    }
    $user= $this->find(session('user_id'));


    if($user['password']!=MD5($oldpassword))
    {
      $this->error='旧密码错误';
      return false;
    }
    // 更新
    $this->save(array('password'=>md5($password),'uid'=>session('user_id')));
    
    return true;  
  }





	 /**
	 * [_before_update 更新前置方法]
	 * @param  [type] $data    [description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
   	public function _before_update(&$data,$options)
   	{
   		if($data['password']==md5(''))
   			unset($data['password']);
   	}

     /**
     * [_after_insert 插入后置方法]
     * @param  [type] $data    [description]
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function _after_insert($data,$options)
    {
     
      if($data['role']==1)
        D('AuthGroupAccess')->alter_auth_group_access($data['uid']);
    }

    /**
     * [_after_update 更新后置方法]
     * @param  [type] $data    [description]
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function _after_update($data,$options)
    {
     
      if($data['role']==1)
        D('AuthGroupAccess')->alter_auth_group_access($data['uid']);
    }
}