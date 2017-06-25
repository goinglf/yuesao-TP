<?php
namespace User\Controller;
use Common\Controller\MemberbaseController;

class OrderController extends MemberbaseController {
    
    // 用户所有订单
	public function all_list()
    {
        $admin=$this->search($data);
        $this->display();
    }

    public function search($data)
    {
        $where=array();
        $where['is_del']=array('neq', '0');
        //用户id使用session
        if ($_SESSION['user'] !=null ){
            $user_id=$_SESSION['user']['id'];
            $where['user_id']=array('eq',$user_id);
        }
        //获取订单状态
        if ($data['status'] !=null || $data['status']===0){
            $where['order_status']=array('eq',$data['status']);
        }

        //分页
        $count = M('order')->alias(' o ')
            ->join('left join ys_yuesao as y on y.id=o.yuesao_id left join ys_users as u on u.id=o.user_id')
            ->where($where)
            ->count();
        $page = $this->page($count, 4);

        //查询订单/用户名/月嫂
        $order = M('order')->alias(' o ')
            ->field('o.id as oid,o.deposit,o.balance,o.order_status,o.order_name,o.create_at,y.name')
            ->join('left join ys_yuesao as y on y.id=o.yuesao_id left join ys_users as u on u.id=o.user_id')
            ->where($where)
            ->order('create_at desc')
            ->limit($page->firstRow , $page->listRows)
            ->select();
        $this->assign('order',$order);
        $this->assign($this->user);
        $this->assign("page", $page->show('Admin'));

    }

    //    //代付定金
    public function is_deposit(){
        $data= I('get.','','intval');
        $admin=$this->search($data);
        $this->display();
    }
//    //待付余款订单
    public function is_balance(){
        $data= I('get.','','intval');
        $request=I('request.');
        $admin=$this->search($data,$request);
        $this->display();
    }
//
//    //待付评价
    public function is_comments(){
        $data= I('get.','','intval');
        $request=I('request.');
        $admin=$this->search($data,$request);
        $this->display();
    }
//    //订单详情
    public function order_info(){
        $data= I('get.','','intval');
        $where=array();
        $where['is_del']=array('neq', '0');
        $where['o.id']=$data['id'];
        $order = M('order')->alias(' o ')
            ->field('o.id as oid,o.address as oaddress,o.*,y.name,y.level,y.price,y.age,y.experience,y.address,y.avatar,u.user_login,u.mobile')
            ->join('left join ys_yuesao as y on y.id=o.yuesao_id left join ys_users as u on u.id=o.user_id')
            ->where($where)
            ->select();
        if (!$order) {
            $this->error("订单查看失败",U('user/order/all_list'));
        }
        $this->assign('order',$order);
        $this->display();
    }

    //支付订单
    public function pay_order(){
        $data= I('get.','','intval');
        if (empty($data)){
            $this->error('请重新选择订单');
        }
        $id=$data['id'];
        $order=M('order')->alias(' o ')
                ->field('y.name,o.id,o.deposit,o.create_at,o.serve_start,o.serve_end,o.order_name,o.order_status,o.balance')
                ->join('left join ys_yuesao as y on y.id=o.yuesao_id')
                ->where("o.id=$id")
                ->select();
        if (!$order) {
            $this->error("请重新选择订单",U('user/order/all_list'));
        }
       $this->assign('order',$order);
        $this->display();
    }

    //评论
    public function order_comment(){
        $data= I('get.','','intval');
        $where=array();
        $where['is_del']=array('neq', '0');
        $where['o.id']=$data['id'];
        $order = M('order')->alias(' o ')
            ->field('o.id as oid,o.create_at,o.order_name,o.order_status,y.id as yid,y.name,y.level,y.price,y.age,y.experience,y.address,y.avatar,u.id as uid')
            ->join('left join ys_yuesao as y on y.id=o.yuesao_id left join ys_users as u on u.id=o.user_id')
            ->where($where)
            ->select();
        if (!$order) {
            $this->error("请重新选择订单",U('user/order/all_list'));
        }
        $this->assign('order',$order);
        $this->display();
    }
    //评论审核
    public function check_comment()
    {

        if ($_POST) {
            if (empty($_POST['colligate']) || empty($_POST['skill']) || empty($_POST['attitude'])) {
                $this->error('请为我们的服务评分,谢谢');
            }

            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                if (count($_POST['photos_alt']) > 5){
                    $this->error('最多嗮图5张哦');
                }
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
                }
            }

            //添加评论
            $data = array(
                'user_id' => I('post.uid'),
                'yuesao_id' => I('post.yid'),
                'order_id' => I('post.oid'),
                'comprehensive_score' => I('post.colligate'),
                'skill_score' => I('post.skill'),
                'attitude_score' => I('post.attitude'),
                'content' => I('post.comment__input'),
                'create_at' => date('Y-m-d H:i:s'),
            );
            $data['images'] = json_encode($_POST['smeta']);
            if (M('comment')->add($data)) {
                $id=I('post.oid');
                //修改订单状态
               M('order')->where("id=$id")->setField('order_status','4');
                $this->display(':order/comment-success');
            } else {
                $this->error('评价失败,请重试');
            }

        }else{
            $this->error('请重新选择订单');
        }
    }

    //查看评论
    public function commented(){
        $data= I('get.','','intval');
        $where=array();
        $where['is_del']=array('neq', '0');
        $where['o.id']=$data['id'];
        $order = M('order')->alias(' o ')
            ->field('o.id as oid,o.create_at,o.order_name,o.order_status,y.id as yid,y.name,y.level,y.price,y.age,y.experience,y.address,y.avatar,c.*')
            ->join('left join ys_yuesao as y on y.id=o.yuesao_id left join ys_comment as c on c.order_id=o.id')
            ->where($where)
            ->select();
        if ($order[0]['order_status'] !=4){
            $this->error('请重新选择订单');
        }
        $this->assign("photo",json_decode($order[0]['images'],true));
        $this->assign('order',$order);
        $this->display();

    }

    //删除订单
    public function del_order(){
        $id= I('get.id',0,'intval');
        $data['id']=$id;
        $data['is_del']=array('neq', '0');
        if ($id) {
            $result = M("order")->where($data)->save(array('is_del'=>0));
            if ($result) {
                $this->success("订单删除成功",U('user/order/all_list'));
            } else {
                $this->error('订单删除失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }

    }

}
