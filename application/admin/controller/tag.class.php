<?php
defined('IN_YZMPHP') or exit('Access Denied'); 
yzm_base::load_controller('common', 'admin', 0);
yzm_base::load_sys_class('page','',0);

class tag extends common {

	/**
	 * TAG列表
	 */
	public function init() {
		$tag = D('tag');
		$total = $tag->total();
		$page = new page($total, 15);
		$data = $tag->order('id DESC')->limit($page->limit())->select();		
		include $this->admin_tpl('tag_list');
	}


	/**
	 * 添加TAG
	 */
	public function add() {
 		if(isset($_POST['dosubmit'])) {
			$tag = D('tag');
			$_POST['tags'] = str_replace('，', ',', strip_tags($_POST['tags']));
			$arr = array_unique(explode(',', $_POST['tags']));
			foreach($arr as $val){
				$val = trim($val);
				$tagid = $tag->where(array('tag' => $val))->find();
				if(!$tagid){
					$tag->insert(array('tag' => $val, 'total'=>0, 'inputtime'=>SYS_TIME), true);
				}
			}
			return_json(array('status'=>1,'message'=>L('operation_success')));
		}else{
			include $this->admin_tpl('tag_add');
		}
	}
	
	
	/**
	 * 编辑TAG
	 */
 	public function edit() {
		$tag = D('tag');
		if(isset($_POST['dosubmit'])) {
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$total = isset($_POST['total']) ? intval($_POST['total']) : 0;
			$tagv = trim($_POST['tag']);
			$data = $tag->where(array('id!=' => $id, 'tag' => $tagv))->find();
			if($data) return_json(array('status'=>0,'message'=>'TAG标签重复，请修改名称！'));
			if($tag->update(array('tag' => $tagv, 'total' => $total), array('id' => $id), true)){
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json();
			}
			
		}else{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$data = $tag->where(array('id' => $id))->find();
			include $this->admin_tpl('tag_edit');
		}

	}

	
	/**
	 * 删除多个TAG
	 */
	public function del() {
		if($_POST && is_array($_POST['id'])){
			if(D('tag')->delete($_POST['id'], true)){
				showmsg(L('operation_success'));
			}else{
				showmsg(L('operation_failure'));
			}
		}
	}



	/**
	 * TAG标签选择
	 */
	public function public_select() {
		$where = array();
		if(isset($_GET['dosubmit'])){
			$where['tag'] = '%'.$_GET['searinfo'].'%';
		}
		$data = D('tag')->field('id,tag,total')->where($where)->order('id DESC')->select();
		include $this->admin_tpl('tag_select');
	}
	
}