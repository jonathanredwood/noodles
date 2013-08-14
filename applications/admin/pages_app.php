<?php 

class PageApplication extends Application{

	public function __construct(){
		parent::__construct();
	}

	function checkPermission(){
		if(!$this->core->permissions->checkPermission('chatlog_page')){
			header('Location: /');
			exit();
		}
	}
	
	public function run(){
		$this->checkPermission();
		
		$this->content['title'] = 'Page Management- electronoodles.co.uk';
		$this->content['head'][] = '<link rel="stylesheet" type="text/css" href="/themes/default/css/cms.css"/>';
		
		$this->content['output'] = '';
		
		$Pages = new Pages($this->core);
		
		$parentid = $Pages->get_parent_id($_GET['pageid']);
		$this->content['parentid'] = $parentid;

		
		if(isset($_GET['action'])){
			
			// Actions
			
			if($_GET['action'] == 'add'){
				$this->content['editor'] = true;
				$this->content['applications'] = $Pages->list_applications();
		
				if(isset($_POST['submit'])){
					$menuShow = ($_POST['menuShow'] == 'on')? true:false;
					$Pages->create_page($_POST['application'], $_POST['menuTitle'], $_POST['title'], $_POST['url'], $_POST['copy'], $menuShow);
					//header('Location: /pages');
					//exit();
				}
			}
			
			if(isset($_GET['pageid']) && is_numeric($_GET['pageid'])){
				
				if($_GET['action'] == 'edit'){
					
					if(isset($_POST['submit'])){
						$menuShow = ($_POST['menuShow'] == 'on')? true:false;
						$Pages->edit_page($_GET['pageid'],$_POST['application'], $_POST['menuTitle'], $_POST['title'], $_POST['url'], $_POST['copy'], $menuShow);
						//header('Location: /pages');
						//exit();
					}
					
					// Include editing stuff
					$this->content['editor'] = true;
					$this->content['head'][] = '<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>';
					$this->content['head'][] = '<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>';
					$this->content['pagedata'] = $Pages->get_page($_GET['pageid']);
					$this->content['applications'] = $Pages->list_applications();
				}
				
				if($_GET['action'] == 'orderup'){	
					$Pages->page_order_up($_GET['pageid']);
					header('Location: /pages?pageid='.$parentid);
					exit();
				}
				
				if($_GET['action'] == 'orderdown'){
					$Pages->page_order_down($_GET['pageid']);
					header('Location: /pages?pageid='.$parentid);
					exit();
				}
			}
			
		}else{
			
			// List pages
	
			if(isset($_GET['pageid']) && is_numeric($_GET['pageid'])){
				$pagedata = $Pages->get_child_pages($_GET['pageid']);
			}else{
				$pagedata = $Pages->get_child_pages();
			}
			
			$table = array();
					
			foreach($pagedata as $data){
							
				$table[] = array(
						'Page'	=> array('show' => true, 'text' => $data['menuTitle'], 'href' => '/'.$this->request.'?pageid='.$data['id']),
						'URL'	=> array('show' => true, 'text' => $data['url'], 'href' => '/'.$data['url']),
						'Up'	=> array('show' => true, 'image' => array('src' => '/themes/default/graphics/arrows/up32.png', 'width' => '16'), 'href' => '?pageid='.$data['id'].'&action=orderup'),
						'Down'	=> array('show' => true, 'image' => array('src' => '/themes/default/graphics/arrows/down32.png', 'width' => '16'), 'href' => '?pageid='.$data['id'].'&action=orderdown'),
						'Edit'	=> array('show' => true, 'center' => true, 'image' => array('src' => '/themes/default/graphics/list.png', 'width' => '16'), 'href' => '?pageid='.$data['id'].'&action=edit')
				);
			}
			
			$view = new View();
			$this->content['output'] = $view->generate('core/UIElements/Table.php', array('id' => 'list-table', 'data' => $table));
			

		}
	}
}

