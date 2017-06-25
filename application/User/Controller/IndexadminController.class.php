<?php
namespace User\Controller;

use Common\Controller\AdminbaseController;

class IndexadminController extends AdminbaseController {

    // 后台本站用户列表
    public function index(){
        $where=array();
        $request=I('request.');
        $where['user_status']=array('neq', '3');
        //查询手机号
        if(!empty($request['mobile'])){
            $mobile=$request['mobile'];
            $where['mobile']=array('like', "%$mobile%");
        }
        //查询关键字
        if(!empty($request['keyword'])){
            $keyword=$request['keyword'];
            $keyword_complex=array();
            $keyword_complex['user_login']  = array('like', "%$keyword%");
            $keyword_complex['user_nicename']  = array('like',"%$keyword%");
            $keyword_complex['user_email']  = array('like',"%$keyword%");
            $keyword_complex['_logic'] = 'or';
            $where['_complex'] = $keyword_complex;
        }

        $users_model=M("Users");
        $count=$users_model->where($where)->count();
        $page = $this->page($count,2);

        $list = $users_model
            ->alias(' u ')
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->field('u.id as userid,u.user_login as name ,u.mobile,u.user_email,u.avatar,u.last_login_ip as ip,u.last_login_time as time,u.user_status')
            ->select();

        $this->assign('list', $list);
        $this->assign("page", $page->show('Admin'));
        $this->display(":index");
    }


    //查询用户信息
    public function user_info(){
        $id= I('get.id',0,'intval');

        $list=M('users')->where("id=$id")->find();
        $this->assign('list', $list);

        $this->display(":user_info");
    }

    //修改会员信息

    public function edit_user(){
        $id=I('post.id',0,'intval');
        $user_login=I('post.user_login','','strip_tags');
        $user_nicename=I('post.user_nicename','','strip_tags');
        $sex=I('post.sex','','strip_tags');
        $birthday=I('post.birthday','','strip_tags');
        $qq=I('post.qq','','strip_tags');
        $wechat=I('post.wechat','','strip_tags');
        $mobile=I('post.mobile','','strip_tags');
        $avatar=I('post.avatar','');

        $sex =='男'?$sex=0:$sex=1;

        if ($user_nicename == '未填写'|| $user_nicename == '')
        {
            $user_nicename='';
        }
        //判断生日
        if ($birthday == '未填写'|| $birthday == '')
        {
            $birthday='';
        }

        if ($qq == '未填写'|| $qq == '')
        {
            $qq='';
        }
        if ($wechat == '未填写'|| $wechat == '')
        {
            $wechat='';
        }
        if ($mobile == '未填写'|| $mobile == '')
        {

            $this->error('手机号不能为空！');
        }

        //手机号只能为数字
        if(!is_numeric($mobile)){
            $this->error('手机格式不正确');
        }

        $data['user_login']=$user_login;
        $data['user_nicename']=$user_nicename;
        $data['sex']=$sex;
        $data['birthday']=$birthday;
        $data['qq']=$qq;
        $data['wechat']=$wechat;
        $data['mobile']=$mobile;
        $data['avatar']=$avatar;

        $user=M('users')->where("id=$id")->save($data);
        if ($user) {
            $this->success("修改成功！", U("indexadmin/index"));
        } else {
            $this->error('修改失败');
        }

    }


    //查询订单信息
    public function order_info(){

        $data= I('get.','');
        $request=I('request.');
        $res=A('Order/Index');
        $res->search($data,$request);
        $this->assign('id',$request['user_id']);
        $this->display(":order_info");



    }

    //伪删除订单
    public function del_order(){
        $id= I('get.id',0,'intval');

        if ($id) {
            $result = M("order")->where("id=$id")->save(array('order_status'=>5));
            if ($result) {
                $this->success("订单删除成功！");
            } else {
                $this->error('订单删除失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }



    //伪删除用户
    public function del_user(){
        $id= I('get.id',0,'intval');
        if ($id) {
            $result = M("Users")->where("id=$id")->save(array('user_status'=>3));
            if ($result) {
                $this->success("会员删除成功！");
            } else {
                $this->error('会员删除失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }


    // 后台本站用户禁用
    public function ban(){
        $id= I('get.id',0,'intval');
        if ($id) {
            $result = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('user_status',0);
            if ($result) {
                $this->success("会员拉黑成功！", U("indexadmin/index"));
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }




    // 后台本站用户启用
    public function cancelban(){
        $id= I('get.id',0,'intval');
        if ($id) {
            $result = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('user_status',1);
            if ($result) {
                $this->success("会员启用成功！", U("indexadmin/index"));
            } else {
                $this->error('会员启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }
}



