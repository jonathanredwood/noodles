<?php 

class PageApplication extends Application{

	public function __construct(){
		parent::__construct();
	}
	
	public function run(){
		
		$this->content['title'] = 'Page Management- electronoodles.co.uk';
		$this->content['head'] = array('<link rel="stylesheet" type="text/css" href="/themes/default/css/cms.css"/>');
		
		$this->content['output'] = '';
		
		$parentid = $this->get_parent_id($_GET['pageid']);
		$this->content['parentid'] = $parentid;

		
		if(isset($_GET['action'])){
			
			// Actions
			
			if($_GET['action'] == 'add'){
				$this->content['editor'] = true;
				$this->content['applications'] = $this->list_applications();
		
				if(isset($_POST['submit'])){
					$menuShow = ($_POST['menuShow'] == 'on')? true:false;
					$this->create_page($_POST['application'], $_POST['menuTitle'], $_POST['title'], $_POST['url'], $_POST['copy'], $menuShow);
					//header('Location: /pages');
					//exit();
				}
			}
			
			if(isset($_GET['pageid']) && is_numeric($_GET['pageid'])){
				
				if($_GET['action'] == 'edit'){
					
					if(isset($_POST['submit'])){
						$menuShow = ($_POST['menuShow'] == 'on')? true:false;
						$this->edit_page($_GET['pageid'],$_POST['application'], $_POST['menuTitle'], $_POST['title'], $_POST['url'], $_POST['copy'], $menuShow);
						//header('Location: /pages');
						//exit();
					}
					
					// Include editing stuff
					$this->content['editor'] = true;
					$this->content['pagedata'] = $this->get_page($_GET['pageid']);
					$this->content['applications'] = $this->list_applications();
				}
				
				if($_GET['action'] == 'orderup'){	
					$this->page_order_up($_GET['pageid']);
					header('Location: /pages?pageid='.$parentid);
					exit();
				}
				
				if($_GET['action'] == 'orderdown'){
					$this->page_order_down($_GET['pageid']);
					header('Location: /pages?pageid='.$parentid);
					exit();
				}
			}
			
		}else{
			
			// List pages
	
			if(isset($_GET['pageid']) && is_numeric($_GET['pageid'])){
				$pagedata = $this->get_child_pages($_GET['pageid']);
			}else{
				$pagedata = $this->get_child_pages();
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
			$this->content['output'] = $view->generate('/UIElements/Table.php', array('id' => 'list-table', 'data' => $table));
			

		}
	}
		
	/**
	 * Get the page content for a specified ID
	 * @param int $id
	 */
	public function get_page($id){
		$this->core->db->prepare("SELECT * FROM pages WHERE id = :id");
		$this->core->db->bind_value(':id', $id, 'int');
		$this->core->db->execute();
		return $this->core->db->prepQueryFirst();
	}
	
	public function get_parent_id($id){
		$url = $this->get_url_from_id($id);
		$parenturl =  implode('/',explode('/',$url,-1));
		return $this->get_id_from_url($parenturl);
	}
	
	/**
	 * Get an array of all the applications
	 */
	public function list_applications(){
		return $this->core->db->queryAll("SELECT * FROM applications ORDER BY displayname");
	}
	
	/**
	 * Create a new page
	 * @param int $application
	 * @param string $menutitle
	 * @param string $title
	 * @param string $url
	 * @param string $copy
	 * @param int $menushow
	 */
	public function create_page($application, $menutitle, $title, $url, $copy, $menushow){
		$this->core->db->prepare("INSERT INTO pages (application, url, menuTitle, title, copy, menuShow) VALUES (:application, :url, :menutitle, :title, :copy, :menushow) ");
		$this->core->db->bind_value(':application', $application, 'int');
		$this->core->db->bind_value(':menutitle', $menutitle, 'string');
		$this->core->db->bind_value(':title', $title, 'string');
		$this->core->db->bind_value(':url', $url, 'string');
		$this->core->db->bind_value(':copy', $copy, 'string');
		$this->core->db->bind_value(':menushow', $menushow, 'int');
		$this->core->db->execute();
		// Set the ordering to the ID of the page
		$this->core->db->query("UPDATE pages SET ordering = ".$this->core->db->lastInsertId()." WHERE id = ".$this->core->db->lastInsertId());
	}
	
	/**
	 * Edit page
	 * @param int $application
	 * @param string $menutitle
	 * @param string $title
	 * @param string $url
	 * @param string $copy
	 * @param int $menushow
	 */
	public function edit_page($id, $application, $menutitle, $title, $url, $copy, $menushow){
		$this->core->db->prepare("UPDATE pages SET 
									application = :application, 
									url = :url,
									menuTitle = :menutitle,
									title = :title, 
									copy = :copy, 
									menuShow = :menushow
										WHERE id = :id");
		
		$this->core->db->bind_value(':id', $id, 'int');
		$this->core->db->bind_value(':application', $application, 'int');
		$this->core->db->bind_value(':menutitle', $menutitle, 'string');
		$this->core->db->bind_value(':title', $title, 'string');
		$this->core->db->bind_value(':url', $url, 'string');
		$this->core->db->bind_value(':copy', $copy, 'string');
		$this->core->db->bind_value(':menushow', $menushow, 'int');
		$this->core->db->execute();
	}
	
	/**
	 * Gets the URL for a specified ID
	 * @param int $id
	 * @return string|boolean
	 */
	public function get_url_from_id($id){
		$this->core->db->prepare("SELECT url FROM pages WHERE id = :parent");
		$this->core->db->bind_value(':parent', $id, 'int');
		$this->core->db->execute();
		if($result = $this->core->db->prepQueryFirst()){
			return $result['url'];
		}else{
			return false;
		}
	}
	
	/**
	 * Gets the ID for a specified url
	 * @param string $url
	 * @return int|boolean
	 */
	public function get_id_from_url($url){
		if(!empty($url)){
			$this->core->db->prepare("SELECT id FROM pages WHERE url = :url");
			$this->core->db->bind_value(':url', $url, 'string');
			$this->core->db->execute();
			if($result = $this->core->db->prepQueryFirst()){
				return $result['id'];
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * Get the ordering position for the specified ID
	 * @param int $id
	 * @return int|boolean
	 */
	public function get_order_from_id($id){
		$this->core->db->prepare("SELECT ordering FROM pages WHERE id = :parent");
		$this->core->db->bind_value(':parent', $id, 'int');
		$this->core->db->execute();
		if($result = $this->core->db->prepQueryFirst()){
			return $result['ordering'];
		}else{
			return false;
		}
	}
	
	/**
	 * Gets the id, URL and menu title of child pages of the given ID, if none is given, then the root pages are fetched
	 * @param int $id
	 */
	public function get_child_pages($id = false){
		if($id){
			// Get parent
			$parenturl = $this->get_url_from_id($id);

			// Get the child pages
			$this->core->db->prepare("SELECT id, url, menuTitle, ordering FROM pages
										WHERE url LIKE CONCAT(:parent, '%')
										AND LENGTH(url) - LENGTH(REPLACE(url, '/', '')) = :depth
										AND menuShow = 1
										ORDER BY ordering");

			$this->core->db->bind_value(':parent', $parenturl, 'string');
			$this->core->db->bind_value(':depth', substr_count($parenturl,'/')+1, 'int');
			$this->core->db->execute();
			return $this->core->db->prepQueryAll();
		}else{
			return $this->core->db->queryAll("SELECT id, url, menuTitle, ordering FROM pages 
												WHERE LENGTH(url) - LENGTH(REPLACE(url, '/', '')) = 0 
												AND menuShow = 1 
												ORDER BY ordering");
		}
		
	}
	
	/**
	 * Gets the id, URL and menu title of sibling pages of the given ID
	 * @param int $id
	 */
	public function get_sibling_pages($id){
		// Work out the page's parent
		$url = $this->get_url_from_id($id);
		$parenturl =  implode('/',explode('/',$url,-1));
		
		if(empty(trim($parenturl))){
			//Root pages
			return $this->get_child_pages();
		}else{
			// Get the child pages
			$this->core->db->prepare("SELECT id, url, menuTitle, ordering FROM pages 
										WHERE url LIKE CONCAT(:parent,'%') 
										AND LENGTH(url) - LENGTH(REPLACE(url, '/', '')) = :depth
										AND url != :parent 
										AND menuShow = 1
										ORDER BY ordering");
			$this->core->db->bind_value(':parent', $parenturl, 'string');
			$this->core->db->bind_value(':depth', substr_count($parenturl,'/')+1, 'int');
			$this->core->db->execute();
			return $this->core->db->prepQueryAll();
		}
	}
	
	/**
	 * Swap the ordering of page with the specified ID with the page with the next ordering value up
	 * @param int $id
	 * @return boolean
	 */
	public function page_order_up($id){
		$siblings = $this->get_sibling_pages($id);
		$siblings = array_reverse($siblings);
			
		$current_ordering = $this->get_order_from_id($id);
			
		foreach($siblings as $sibling){
			if($sibling['ordering'] < $current_ordering){
				// Swap the ordering
				$this->core->db->prepare("UPDATE pages SET ordering = :ordering WHERE id = :id");
					
				$this->core->db->bind_value(':ordering', $sibling['ordering'], 'int');
				$this->core->db->bind_value(':id', $id, 'int');
				$this->core->db->execute();
					
				$this->core->db->bind_value(':ordering', $current_ordering, 'int');
				$this->core->db->bind_value(':id', $sibling['id'], 'int');
				$this->core->db->execute();
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Swap the ordering of page with the specified ID with the page with the next ordering value down
	 * @param int $id
	 * @return boolean
	 */
	public function page_order_down($id){
		$siblings = $this->get_sibling_pages($id);
	
		$current_ordering = $this->get_order_from_id($id);
	
		foreach($siblings as $sibling){
			if($sibling['ordering'] > $current_ordering){
				// Swap the ordering
				$this->core->db->prepare("UPDATE pages SET ordering = :ordering WHERE id = :id");
	
				$this->core->db->bind_value(':ordering', $sibling['ordering'], 'int');
				$this->core->db->bind_value(':id', $id, 'int');
				$this->core->db->execute();
					
				$this->core->db->bind_value(':ordering', $current_ordering, 'int');
				$this->core->db->bind_value(':id', $sibling['id'], 'int');
				$this->core->db->execute();
				return true;
			}
		}
		return false;
	}
}

