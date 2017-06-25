<?php
namespace User\Controller;

use Common\Controller\MemberbaseController;

class HistoryController extends MemberbaseController{
	
    // 我的浏览历史记录
	public function index(){

	    $this->display(':footprint');
	}



}