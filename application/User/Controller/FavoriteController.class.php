<?php
namespace User\Controller;

use Common\Controller\MemberbaseController;

class FavoriteController extends MemberbaseController{
	
    // 前台个人中心我的收藏列表
	public function index(){
		$uid=sp_get_current_userid();
		$user_favorites_model=M("UserFavorites");
		$where=array("uid"=>$uid);
		
		$count=$user_favorites_model->where($where)->count();
		
		$page=$this->page($count,10);
		
		$favorites=$user_favorites_model->where($where)
		->order("createtime desc")
		->limit($page->firstRow,$page->listRows)
		->select();
		
		$this->assign("page",$page->show("default"));
		
		$this->assign("favorites",$favorites);
		$this->display(":favorite");
	}
	
	// 用户收藏
	public function do_favorite(){
		$key=sp_authcode(I('post.key'));
		if($key){
			$authkey=C("AUTHCODE");
			$key=explode(" ", $key);
			$authcode=$key[0];
			if($authcode==C("AUTHCODE")){
				$table=$key[1];
				$object_id=$key[2];
				$post=I("post.");
				unset($post['key']);
				$post['table']=$table;
				$post['object_id']=$object_id;
				
				$uid=sp_get_current_userid();
				$post['uid']=$uid;
				$user_favorites_model=M("UserFavorites");
				$find_favorite=$user_favorites_model->where(array('table'=>$table,'object_id'=>$object_id,'uid'=>$uid))->find();
				if($find_favorite){
					$this->error("亲，您已收藏过啦！");
				}else {
					$post['createtime']=time();
					$result=$user_favorites_model->add($post);
					if($result){
						$this->success("收藏成功！");
					}else {
						$this->error("收藏失败！");
					}
				}
			}else{
				$this->error("非法操作，无合法密钥！");
			}
		}else{
			$this->error("非法操作，无密钥！");
		}
		
	}
	
	// 用户取消收藏
	public function delete_favorite(){
		$id=I("get.id",0,"intval");
		$uid=sp_get_current_userid();
		$post['uid']=$uid;
		$user_favorites_model=M("UserFavorites");
		$result=$user_favorites_model->where(array('id'=>$id,'uid'=>$uid))->delete();
		if($result){
			$this->success("取消收藏成功！");
		}else {
			$this->error("取消收藏失败！");
		}
	}


	//收藏
	public function mark(){

	    //获取月嫂信息
	    $uid=$_SESSION['user']['id'];
	    $ys=M('favorite')
            ->field('ys_favorite.id,y.id as yid,y.name,y.level,y.age,y.experience,y.price,y.avatar,y.address,y.introduce')
            ->join('join ys_yuesao as y on ys_favorite.yuesao_id=y.id')
            ->where("user_id=$uid")->select();

        $this->assign('ys',$ys);
	    $this->assign('avatar',$_SESSION['user']['avatar']);
	    $this->display(':mark');


    }

    //取消收藏
    public function del_mark(){
        $id=I("get.id",0,"intval");
        $uid=$_SESSION['user']['id'];
        //判断是否存在信息
        if (M('favorite')->where("id=$id")->count() == 0){
            $this->error('请重试');
        }

        //删除月嫂
        $data=M('favorite')->where(array('id'=>$id,'user_id'=>$uid))->delete();
        if ($data){
            $this->success('删除成功');
        }else{
            $this->success('删除失败');
        }
    }


}