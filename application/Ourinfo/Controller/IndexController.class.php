<?php
namespace Ourinfo\Controller;

use Common\Controller\HomebaseController;

class IndexController extends HomebaseController {

    //关于我们
    public function about()
    {
        $this->display(':about');
    }
    //联系我们
    public function contact()
    {
        $this->display(':contact');
    }



}