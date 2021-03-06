<?php
namespace User\Controller;

use Common\Controller\MemberbaseController;

class ProfileController extends MemberbaseController {
	
	function _initialize(){
		parent::_initialize();
	}
	
    // 编辑用户资料
//	public function edit() {
//		$this->assign($this->user);
//    	$this->display();
//    }
    
//    // 编辑用户资料提交
    public function edit_post() {
    	if(IS_POST){

            if(empty(I('post.mobile'))){
                $this->error("手机号不能为空！",U("user/center/index"));
            }
    	    //验证密码
//            $password=I('post.user_pass');
//            if(empty($password)){
//                $this->error("新密码不能为空！",U("user/center/index"));
//            }
//
//            if($password !=I('post.repassword')){
//                $this->error("密码输入不一致！",U("user/center/index"));
//            }
    		$_POST['id']=$this->userid;
    		if ($this->users_model->field('id,user_login,sex,qq,wechat,mobile')->create()!==false) {
				if ($this->users_model->save()!==false) {
					$this->user=$this->users_model->find($this->userid);
					sp_update_current_user($this->user);
//					echo $this->users_model->fetchsql(true)->save();
					$this->success("保存成功！",U("user/center/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->users_model->getError());
			}
    	}

    }
    
    // 个人中心修改密码
//    public function password() {
//		$this->assign($this->user);
//    	$this->display();
//    }
//
//    // 个人中心修改密码提交
    public function password_post() {
    	if (IS_POST) {
//    	    $old_password=I('post.old_password');
//    		if(empty($old_password)){
//    			$this->error("原始密码不能为空！");
//    		}

    		$password=I('post.password');
    		if(empty($password) || empty(I('post.repassword'))){
                $this->error("新密码不能为空！");
            }
            if(strlen($password) <6 || strlen(I('post.repassword')) <6){
                $this->error("密码不能少于6位数！");
            }

    		$uid=sp_get_current_userid();
    		$admin=$this->users_model->where(array('id'=>$uid))->find();
    			if($password==I('post.repassword')){
    				if(sp_compare_password($password, $admin['user_pass'])){
    					$this->error("新密码不能和原始密码相同！");
    				}else{
    					$data['user_pass']=sp_password($password);
    					$data['id']=$uid;
    					$r=$this->users_model->save($data);
    					if ($r!==false) {
                            session("user",null);
    						$this->success("修改成功！请重新登录",redirect("user/login/index"),3);
    					} else {
    						$this->error("修改失败！");
    					}
    				}
    			}else{
    				$this->error("密码输入不一致！");
    			}
    	}

    }
//    public function edit_post() {
//        if(IS_POST){
//            $_POST['id']=$this->userid;
//            if ($this->users_model->field('id,user_nicename,sex,birthday,user_url,signature')->create()!==false) {
//                if ($this->users_model->save()!==false) {
//                    $this->user=$this->users_model->find($this->userid);
//                    sp_update_current_user($this->user);
//                    $this->success("保存成功！",U("user/profile/edit"));
//                } else {
//                    $this->error("保存失败！");
//                }
//            } else {
//                $this->error($this->users_model->getError());
//            }
//        }
//
//    }

    // 个人中心修改密码
//    public function password() {
//        $this->assign($this->user);
//        $this->display();
//    }

    // 个人中心修改密码提交
//    public function password_post() {
//        if (IS_POST) {
//            $old_password=I('post.old_password');
//            if(empty($old_password)){
//                $this->error("原始密码不能为空！");
//            }
//
//            $password=I('post.password');
//            if(empty($password)){
//                $this->error("新密码不能为空！");
//            }
//
//            $uid=sp_get_current_userid();
//            $admin=$this->users_model->where(array('id'=>$uid))->find();
//            if(sp_compare_password($old_password, $admin['user_pass'])){
//                if($password==I('post.repassword')){
//                    if(sp_compare_password($password, $admin['user_pass'])){
//                        $this->error("新密码不能和原始密码相同！");
//                    }else{
//                        $data['user_pass']=sp_password($password);
//                        $data['id']=$uid;
//                        $r=$this->users_model->save($data);
//                        if ($r!==false) {
//                            $this->success("修改成功！");
//                        } else {
//                            $this->error("修改失败！");
//                        }
//                    }
//                }else{
//                    $this->error("密码输入不一致！");
//                }
//
//            }else{
//                $this->error("原始密码不正确！");
//            }
//        }
//
//    }
    // 第三方账号绑定
    public function bang(){
    	$oauth_user_model=M("OauthUser");
    	$uid=sp_get_current_userid();
    	$oauths=$oauth_user_model->where(array("uid"=>$uid))->select();
    	$new_oauths=array();
    	foreach ($oauths as $oa){
    		$new_oauths[strtolower($oa['from'])]=$oa;
    	}
    	$this->assign("oauths",$new_oauths);
    	$this->display();
    }
    
    // 用户头像编辑
    public function avatar(){
		$this->assign($this->user);
    	$this->display();
    }
    
    // 用户头像上传
    public function avatar_upload(){
    	$config=array(
			'rootPath' => './'.C("UPLOADPATH"),
			'savePath' => './avatar/',
			'maxSize' => 512000,//500K
			'saveName'   =>    array('uniqid',''),
			'exts'       =>    array('jpg', 'png', 'jpeg'),
			'autoSub'    =>    false,
    	);
    	$upload = new \Think\Upload($config,'Local');//先在本地裁剪
    	$info=$upload->upload();
    	//开始上传
    	if ($info) {
    	//上传成功
    	//写入附件数据库信息
    		$first=array_shift($info);
    		$file=$first['savename'];
    		session('avatar',$file);
    		$this->ajaxReturn(sp_ajax_return(array("file"=>$file),"上传成功！",1),"AJAX_UPLOAD");
    	} else {
    		//上传失败，返回错误
    		$this->ajaxReturn(sp_ajax_return(array(),$upload->getError(),0),"AJAX_UPLOAD");
    	}
    }
    
    // 用户头像裁剪
    public function avatar_update(){
        $session_avatar=session('avatar');
    	if(!empty($session_avatar)){
    		$targ_w = I('post.w',0,'intval');
    		$targ_h = I('post.h',0,'intval');
    		$x = I('post.x',0,'intval');
    		$y = I('post.y',0,'intval');
    		$jpeg_quality = 90;
    		
    		$avatar=$session_avatar;
    		$avatar_dir=C("UPLOADPATH")."avatar/";
    		
    		$avatar_path=$avatar_dir.$avatar;
    		
    		$image = new \Think\Image();
    		$image->open($avatar_path);
    		$image->crop($targ_w, $targ_h,$x,$y);
    		$image->save($avatar_path);
    		
    		$result=true;
    		
    		$file_upload_type=C('FILE_UPLOAD_TYPE');
    		if($file_upload_type=='Qiniu'){
    		    $upload = new \Think\Upload();
    		    $file=array('savepath'=>'','savename'=>'avatar/'.$avatar,'tmp_name'=>$avatar_path);
    		    $result=$upload->getUploader()->save($file);
    		}
    		if($result===true){
    		    $userid=sp_get_current_userid();
    		    $result=$this->users_model->where(array("id"=>$userid))->save(array("avatar"=>'avatar/'.$avatar));
    		    session('user.avatar','avatar/'.$avatar);
    		    if($result){
    		        $this->success("头像更新成功！");
    		    }else{
    		        $this->error("头像更新失败！");
    		    }
    		}else{
    		    $this->error("头像保存失败！");
    		}
    		
    	}
    }
    
    // 保存用户头像
    public function do_avatar() {
		$imgurl=I('post.imgurl');
		//去'/'
		$imgurl=str_replace('/','',$imgurl);
		$old_img=$this->user['avatar'];
		$this->user['avatar']=$imgurl;
		$res=$this->users_model->where(array("id"=>$this->userid))->save($this->user);		
		if($res){
			//更新session
			session('user',$this->user);
			//删除旧头像
			sp_delete_avatar($old_img);
		}else{
			$this->user['avatar']=$old_img;
			//删除新头像
			sp_delete_avatar($imgurl);
		}
		$this->ajaxReturn($res);
	}       
}