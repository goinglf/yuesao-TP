<?php
namespace Yuesao\Controller;

use Common\Controller\AdminbaseController;

class IndexController extends AdminbaseController
{

    /*显示主页*/
    public function index()
    {
        $this->display();
    }

    //添加月嫂
    public function add_ys()
    {

        if ($_POST) {
            $avatar = I('post.avatar', '');
            $name = I('post.name', '', 'strip_tags');
            $age = I('post.age', '', 'strip_tags');
            $address = I('post.address', '', 'strip_tags');
            $price = I('post.price', '', 'strip_tags');
            $is_recommend = I('post.is_recommend', '', 'strip_tags');
            $baby_num = I('post.baby_num', '', 'strip_tags');
            $mobile = I('post.mobile', '', 'strip_tags');
            $level = I('post.level', '', 'strip_tags');
            $experience = I('post.experience', '');
            $certificate = I('post.certificate', '');
            $skill = I('post.skill', '');
            $feature = I('post.feature', '');
            $introduce = I('post.introduce', '');


            //验证
            if (!is_numeric($mobile)) {
                $this->error('手机格式不正确');
            }
            if (!is_numeric($age) || $age<20 || $age>100) {
                $this->error('请输入正确的年龄');
            }
            if (!is_numeric($baby_num)) {
                $this->error('请输入正确的宝宝数量');
            }
            if (!is_numeric($price)) {
                $this->error('请输入正确的价格');
            }
            if (!is_numeric($level) || $level<1 || $level>5) {
                $this->error('请输入正确的等级');
            }

            if (!is_numeric($experience) || $experience<0 || $experience>100) {
                $this->error('请输入正确的经验值');
            }

            //头像
            $_POST['avatar']['thumb'] = sp_asset_relative_url($_POST['avatar']['thumb']);
            $article=I("post.");
            $article['avatar']=json_encode($_POST['avatar']);
            $article['introduce']=htmlspecialchars_decode($article['introduce']);
            $result=M('yuesao')->add($article);
            //基本信息添加成功
            if ($result){
            //查找最新的ID,刚插入的id
            //相册
            if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
                $ys=M('yuesao')->order('id desc')->find();
                foreach ($_POST['photos_url'] as $key=>$url){
                    //相册地址
                    $photourl=sp_asset_relative_url($url);
                    $res=M('yuesao_images')->add(array("yuesao_id"=>$ys['id'],"images"=>$photourl));
                }
                if ($res) {
                    $this->success("添加成功！");
                } else {
                    $this->error("添加失败！");
                }
            }else{
                $this->success("添加成功！");
            }
        }
    }
        $this->display();
    }

//    public function addys()
//    {
//
//        if ($_POST) {
//            $avatar = I('post.avatar', '');
//            $name = I('post.name', '', 'strip_tags');
//            $age = I('post.age', '', 'strip_tags');
//            $address = I('post.address', '', 'strip_tags');
//            $price = I('post.price', '', 'strip_tags');
//            $is_recommend = I('post.is_recommend', '', 'strip_tags');
//            $baby_num = I('post.baby_num', '', 'strip_tags');
//            $mobile = I('post.mobile', '', 'strip_tags');
//            $level = I('post.level', '', 'strip_tags');
//            $experience = I('post.experience', '');
//            $certificate = I('post.certificate', '');
//            $skill = I('post.skill', '');
//            $feature = I('post.feature', '');
//            $introduce = I('post.introduce', '');
//
//
//            //验证
////            if (!is_numeric($mobile)) {
////                $this->error('手机格式不正确');
////            }
////            if (!is_numeric($age) || $age<20 || $age>100) {
////                $this->error('请输入正确的年龄');
////            }
////            if (!is_numeric($baby_num)) {
////                $this->error('请输入正确的宝宝数量');
////            }
////            if (!is_numeric($price)) {
////                $this->error('请输入正确的价格');
////            }
////            if (!is_numeric($level) || $level<1 || $level>5) {
////                $this->error('请输入正确的等级');
////            }
////
////            if (!is_numeric($experience) || $experience<0 || $experience>100) {
////                $this->error('请输入正确的经验值');
////            }
//
//            //相册
////            if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
////                foreach ($_POST['photos_url'] as $key=>$url){
////                    $photourl=sp_asset_relative_url($url);
////                    $_POST['avatar']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
////                }
////            }
//
//            //头像
//            $_POST['avatar']['thumb'] = sp_asset_relative_url($_POST['avatar']['thumb']);
//            $article=I("post.");
//            $article['avatar']=json_encode($_POST['avatar']);
//            $article['introduce']=htmlspecialchars_decode($article['introduce']);
//            $result=M('yuesao')->add($article);
//            if ($result) {
//                $this->success("添加成功！");
//            } else {
//                $this->error("添加失败！");
//            }
//
//        }
//
//        $this->display();
//    }
    //后台月嫂列表




    public function ys_list()
    {
        $where = array();
        $request = I('request.');
        //排序条件
        $order=array();
        if (!empty($_GET['data'])){
            $data=$_GET['data'];
            $sort=$_GET['sort'];
            $order["$data"]=$sort;
        }

        //查询手机号
        if (!empty($request['mobile'])) {
            $mobile = $request['mobile'];
            $where['mobile'] = array('like', "%$mobile%");
        }
        //查询关键字
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];
            $keyword_complex = array();
            $keyword_complex['name'] = array('like', "%$keyword%");
            $where['_complex'] = $keyword_complex;
        }
        //将post_status状态不等于3的显示出来,3为删除状态
        $where['post_status']=array('neq', '3');

        $users_model = M("yuesao");
        $count = $users_model->where($where)->count();
        $page = $this->page($count, 5);

        $list = $users_model
            ->where($where)
            ->field('id,name,post_status,istop,level,age,experience,price,mobile,avatar,baby_num,is_recommend')
            ->limit($page->firstRow , $page->listRows)
            ->order($order)
            ->select();

        $this->assign("page", $page->show('Admin'));

        $this->assign('list', $list);

        $this->display();

    }

    //ajax异步输出
//    public function ajax_list(){
//
//        if(isset($_POST['flag'])&&!empty($_POST['flag'])){
//            $flag = $_POST['flag'];
//            if($flag == 1){
//                $order["level"]='desc';
//            }else{
//                $order["level"]='asc';
//            }
//
//            $users_model = M("yuesao");
//            $count = $users_model->count();
//            $page = $this->page($count, 5);
//
//            $list = $users_model
//                ->field('id,name,post_status,istop,level,age,experience,price,mobile,avatar,baby_num,is_recommend')
//                ->limit($page->firstRow , $page->listRows)
//                ->order($order)
//                ->select();
//
//            $this->assign('list', $list);
//            //获取页面HTML代码fetch
//            $data['content'] = $this->fetch();
//            $this->ajaxReturn($data);
//
//        }
//    }
    //编辑月嫂
    public function edit_ys()
    {


        if (!$_POST) {
            $id= I('get.id', '', 'strip_tags');
            //月嫂基本信息
            $list=M('yuesao')->where("id=$id")->select();

            $photo=M('yuesao_images')
                ->field('images')
                ->where("yuesao_id=$id")->select();

            $this->assign("photo",$photo);
            $this->assign("list",$list);
            //相册地址
            $avatar=json_decode($list[0]['avatar'],true);
            $this->assign("avatar",$avatar);

        }else {
            $id = I('post.id', '', 'strip_tags');
            $price = I('post.price', '', 'strip_tags');
            $mobile = I('post.mobile', '', 'strip_tags');
            $baby_num = I('post.baby_num', '', 'strip_tags');
            $level = I('post.level', '', 'strip_tags');
            //验证

            if (!is_numeric($mobile)) {
                $this->error('手机格式不正确');
            }
            if (!is_numeric($baby_num)) {
                $this->error('请输入正确的数量');
            }
            if (!is_numeric($price)) {
                $this->error('请输入正确的价格');
            }
            if (!is_numeric($level)) {
                $this->error('请输入正确的等级');
            }
////
            $_POST['avatar']['thumb'] = sp_asset_relative_url($_POST['avatar']['thumb']);
            $article = I("post.");
            $article['avatar'] = json_encode($_POST['avatar']);
            $article['introduce'] = htmlspecialchars_decode($article['introduce']);
            $result = M('yuesao')->where("id=$id")->save($article);

//            //相册

            if ($result ) {
                $this->success("修改成功！");
            } else {
                $this->error("修改失败！");
            }
        }

        $this->display();
    }


    public function edit_photos()
    {
        if (!$_POST) {
            $id = I('get.id', '', strip_tags());
            $photo = M('yuesao_images')
                ->where("yuesao_id=$id")->select();
//            if (empty($photo)){
//                $ys = M('yuesao')
//                    ->field('id,name')
//                    ->where("id=$id")->find();
//            }
                $ys = M('yuesao')
                    ->field('id,name')
                    ->where("id=$id")->find();
            $this->assign("photo", $photo);
            $this->assign('ys',$ys);
            $this->assign('id', $id);
            $this->display();
        }
    }



    //编辑相册
        public function do_edit_photos()
    {
            $id=I('post.yuesao_id','',strip_tags());


            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                if (!isset($_POST['photo_id'])){
                    foreach ($_POST['photos_url'] as $key => $url) {
                        $photourl = sp_asset_relative_url($url);
                        $res = M('yuesao_images')->add(array("yuesao_id"=>$id,"images" => $photourl));
                    }
                }else{
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                 $res = M('yuesao_images')->where("id=".$_POST['photo_id'][$key])->save(array("images" => $photourl));
                     }
                }
                if ($res) {
                    $this->success("修改成功！");
                } else {
                    $this->error("请重新上传图片");
                }
            }

        }
    //删除相册
    public function del_photos(){
        $id= I('get.id',0,'intval');
        if ($id) {
            $result = M("yuesao_images")->where("id=$id")->delete();
            if ($result) {
                $this->success("删除成功！");
            } else {
                $this->error('删除失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }


//    public function editys()
//    {
//
//
//        if (!$_POST) {
//            $id= I('get.id', '', 'strip_tags');
//            //月嫂基本信息
//            $list=M('yuesao')->where("id=$id")->select();
//
//            $photo=M('yuesao_images')
//                ->field('images')
//                ->where("yuesao_id=$id")->select();
//
//            $this->assign("photo",$photo);
//            $this->assign("list",$list);
//            //相册地址
//            $avatar=json_decode($list[0]['avatar'],true);
//            $this->assign("avatar",$avatar);
//
//        }else {
//            $id = I('post.id', '', 'strip_tags');
//            $price = I('post.price', '', 'strip_tags');
//            $mobile = I('post.mobile', '', 'strip_tags');
//            $baby_num = I('post.baby_num', '', 'strip_tags');
//            $level = I('post.level', '', 'strip_tags');
//            //验证
//
//            if (!is_numeric($mobile)) {
//                $this->error('手机格式不正确');
//            }
//            if (!is_numeric($baby_num)) {
//                $this->error('请输入正确的数量');
//            }
//            if (!is_numeric($price)) {
//                $this->error('请输入正确的价格');
//            }
//            if (!is_numeric($level)) {
//                $this->error('请输入正确的等级');
//            }
//////
//            $_POST['avatar']['thumb'] = sp_asset_relative_url($_POST['avatar']['thumb']);
//            $article = I("post.");
//            $article['avatar'] = json_encode($_POST['avatar']);
//            $article['introduce'] = htmlspecialchars_decode($article['introduce']);
//            $result = M('yuesao')->where("id=$id")->save($article);
//
////            //相册
//
//            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
//                M('yuesao_images')->where("yuesao_id=$id")->delete();
////                $_POST['photos_url'] = $_POST['photos_alt'];
//                foreach ($_POST['photos_url'] as $key => $url) {
//
//                    $photourl = sp_asset_relative_url($url);
//                    $photourl = substr($photourl,strpos($photourl,'default'));
//                    $res = M('yuesao_images')->add(array("yuesao_id" => $id, "images" => $photourl));
//                }
//            }
//            else{
//                $res =  M('yuesao_images')->where("yuesao_id=$id")->delete();
//            }
//
//            if ($res) {
//                $this->success("修改成功！");
//            } else {
//                $this->error("修改失败！");
//            }
//        }
//
//        $this->display();
//    }
    // 月嫂删除
    public function delete(){
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if (M('yuesao')->where(array('id'=>$id))->save(array('post_status'=>3)) !==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }

        if(isset($_POST['ids'])){
//            $ids = I('post.ids/a');
            $ids = I('post.ids');
            if (M('yuesao')->where(array('id'=>array('in',$ids)))->save(array('post_status'=>3))!==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    //月嫂审核
    public function check(){
        if(isset($_POST['ids']) && $_GET["check"]){
            $ids = I('post.ids/a');

            if ( M('yuesao')->where(array('id'=>array('in',$ids)))->save(array('post_status'=>1)) !== false ) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["uncheck"]){
            $ids = I('post.ids/a');

            if ( M('yuesao')->where(array('id'=>array('in',$ids)))->save(array('post_status'=>0)) !== false) {
                $this->success("取消审核成功！");
            } else {
                $this->error("取消审核失败！");
            }
        }
    }

    // 月嫂置顶
    public function top(){
        if(isset($_POST['ids']) && $_GET["top"]){
            $ids = I('post.ids/a');

            if ( M('yuesao')->where(array('id'=>array('in',$ids)))->save(array('istop'=>1))!==false) {
                $this->success("置顶成功！");
            } else {
                $this->error("置顶失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["untop"]){
            $ids = I('post.ids/a');

            if ( M('yuesao')->where(array('id'=>array('in',$ids)))->save(array('istop'=>0))!==false) {
                $this->success("取消置顶成功！");
            } else {
                $this->error("取消置顶失败！");
            }
        }
    }

    // 月嫂推荐
    public function recommend(){
        if(isset($_POST['ids']) && $_GET["recommend"]){
            $ids = I('post.ids/a');

            if ( M('yuesao')->where(array('id'=>array('in',$ids)))->save(array('is_recommend'=>1))!==false) {
                $this->success("推荐成功！");
            } else {
                $this->error("推荐失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["unrecommend"]){
            $ids = I('post.ids/a');

            if ( M('yuesao')->where(array('id'=>array('in',$ids)))->save(array('is_recommend'=>0))!==false) {
                $this->success("取消推荐成功！");
            } else {
                $this->error("取消推荐失败！");
            }
        }
    }


    // 月嫂批量复制
    public function copy1(){
        if(isset($_POST['ids'])){
            $ids = I('post.ids/a');
            $data=array();
            foreach ($ids as $id){
                $find_post=M('yuesao')->field('id,name,mobile,level,age,experoence,baby_num,price,avatar,post_status')->where(array('id'=>$id))->find();
                if($find_post){
//
                    $post_id=M('yuesao')->add($find_post);
//                        if($post_id>0){
//                            array_push($data, array('object_id'=>$post_id,'term_id'=>$term_id));
//                        }
                }
            }

            if ( $post_id !== false) {
                $this->success("复制成功！");
            } else {
                $this->error("复制失败！");
            }
        }
//        }else{
//            $tree = new \Tree();
//            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
//            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
//            $terms = $this->terms_model->order(array("path"=>"ASC"))->select();
//            $new_terms=array();
//            foreach ($terms as $r) {
//                $r['id']=$r['term_id'];
//                $r['parentid']=$r['parent'];
//                $new_terms[] = $r;
//            }
//            $tree->init($new_terms);
//            $tree_tpl="<option value='\$id'>\$spacer\$name</option>";
//            $tree=$tree->get_tree(0,$tree_tpl);
//
//            $this->assign("terms_tree",$tree);
//            $this->display();
    }

}