<?php
namespace Yuesao\Controller;
use Common\Controller\HomebaseController;

class ListController extends HomebaseController {

    //首页展示
    public function index()
    {
        $count=M('yuesao')->count();
        //置顶月嫂信息
        $data=M('yuesao')
            ->field('id,name,level,age,experience,price,avatar,address,introduce')
            ->where('istop=1')
            ->limit(3)->select();

        $this->assign('count',$count);
        $this->assign('data',$data);


        //晒单评价
        $data1=M('comment')->alias('c')
            ->field('c.yuesao_id,y.name,y.avatar,u.user_login,u.mobile,c.create_at,c.content')
            ->join('left join ys_yuesao as y on y.id=c.yuesao_id left join ys_users as u on u.id=c.user_id')
            ->select();

        $this->assign('data1',$data1);
        $this->display();
    }



    //所有月嫂
    public function all_list(){

        $count=M('yuesao')->count();
        $page = $this->page($count,4);
        $data=M('yuesao')
            ->field('id,name,level,age,experience,price,avatar,address')
            ->limit($page->firstRow . ',' . $page->listRows)->select();

        $this->assign("page", $page->show('Admin'));

        $this->assign('count',$count);
        $this->assign('data',$data);
        $this->display();
    }


    //月嫂详情
    public function ys_info(){

        $yid=I('get.id');
//        $ys=M('yuesao')->alias('y')
//            ->field('y.*,i.images')
//            ->join('left join ys_yuesao_images as i on y.id=i.yuesao_id')
//            ->where("y.id=$yid")
//            ->select();

        //月嫂基本信息
        $ys=M('yuesao')->alias('y')
            ->field('y.*,u.mobile as umobile,u.user_login,u.mobile,c.create_at,c.content')
            ->join('left join ys_comment as c on y.id=c.yuesao_id
            left join ys_users as u on c.user_id=u.id')
            ->where("y.id=$yid")
            ->select();

        $this->assign('ys',$ys);
        //月嫂相册
        $photo = M('yuesao_images')
            ->where("yuesao_id=$yid")->select();
        $this->assign('photo',$photo);


        $this->display();
    }
}