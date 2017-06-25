<?php
namespace Order\Controller;

use Common\Controller\AdminbaseController;

class IndexController extends AdminbaseController{

    //后台订单列表
    public function index()
    {
        $data= I('get.','');
        $request=I('request.');
        //订单名称
        $order_name=date('ymds',time()).rand(100000,999999);
        $admin=$this->search($data,$request);
        $this->display();
    }

    //查找
    public function search($data,$request)
    {
        //data订单状态 request筛选请求
        $where=array();
        $where['is_del']=array('neq', '0');
        if(!empty($request['childbirth_start']) ){
            $childbirth_start=$request['childbirth_start'];
            $childbirth_end=$request['childbirth_end'];
            //如果结束时间未赋值,则使用当前时间
            if (empty($childbirth_end)){
                $childbirth_end=date('Y-m-d',time());
            }
            $where['childbirth_start']=array('EGT',$childbirth_start);
            $where['childbirth_end']=array('ELT',$childbirth_end);

        }

        //判断服务期
        if(!empty($request['serve_start']) ){
            $serve_start=$request['serve_start'];
            $serve_end=$request['serve_end'];
            if (empty($serve_end)){
                $serve_end=date('Y-m-d',time());
            }
            $where['serve_start']=array('EGT',$serve_start);
            $where['serve_end']=array('ELT',$serve_end);

        }

        //判断用户id

        if ($data['user_id'] !=null ){
            $where['user_id']=array('eq',$data['user_id']);
        }


        //判断订单状态
        if ($data['status'] !=null || $data['status']===0){
            $where['order_status']=array('eq',$data['status']);
        }


        //分页
        $count = M('order')->alias(' o ')
            ->field('o.id as oid,o.*,y.name,u.user_login')
            ->join('left join ys_yuesao as y on y.id=o.yuesao_id left join ys_users as u on u.id=o.user_id')
            ->where($where)
            ->count();
        $page = $this->page($count, 3);

        //查询订单/用户名/月嫂
        $order = M('order')->alias(' o ')
            ->field('o.id as oid,o.*,y.name,u.user_login')
            ->join('left join ys_yuesao as y on y.id=o.yuesao_id left join ys_users as u on u.id=o.user_id')
            ->where($where)
            ->limit($page->firstRow , $page->listRows)
            ->select();
        $this->assign('order',$order);
        $this->assign("page", $page->show('Admin'));

    }

//    //代付定金
    public function is_deposit(){
        $data= I('get.','','intval');
        $request=I('request.');
        $admin=$this->search($data,$request);
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
//    //代付评价
    public function is_comments(){
        $data= I('get.','','intval');
        $request=I('request.');
        $admin=$this->search($data,$request);
        $this->display();
    }
//    //已完成
    public function order_completed(){
        $data= I('get.','','intval');
        $request=I('request.');
        $admin=$this->search($data,$request);
        $this->display();
    }
//    //已关闭
    public function order_closed(){

        $data= I('get.','','intval');
        $request=I('request.');
        $admin=$this->search($data,$request);
        $this->display();
    }
//
//    //删除订单
//    public function del_order(){
//        $id= I('get.id',0,'intval');
//        if ($id) {
//            $result = M("order")->where("id=$id")->delete();
//            if ($result) {
//                $this->success("订单删除成功！", U("index/index"));
//            } else {
//                $this->error('订单删除失败！');
//            }
//        } else {
//            $this->error('数据传入失败！');
//        }
//
//    }

    //伪删除
    public function del_order(){
        $id= I('get.id',0,'intval');
        if ($id) {
            $result = M("order")->where("id=$id")->save(array('is_del'=>0));
            if ($result) {
                $this->success("订单删除成功！");
            } else {
                $this->error('订单删除失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }

    }



}