<?php

class Pages{
	
	protected $core;
	
	function __construct($core)
	{
		$this->core = $core;
	}
	
	/**
	 * Get the page content for a specified ID
	 * @param int $id
	 */
	public function get_page($id)
	{
		$this->core->db->prepare("SELECT * FROM pages WHERE id = :id");
		$this->core->db->bind_value(':id', $id, 'int');
		$this->core->db->execute();
		return $this->core->db->prepQueryFirst();
	}
	
	public function get_parent_id($id)
	{
		$url = $this->get_url_from_id($id);
		$parenturl =  implode('/',explode('/',$url,-1));
		return $this->get_id_from_url($parenturl);
	}
	
	/**
	 * Get an array of all the applications
	 */
	public function list_applications()
	{
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
	public function create_page($application, $menutitle, $title, $url, $copy, $menushow)
	{
		//check if a page already exists at the given URL
		$this->core->db->prepare("SELECT id FROM pages WHERE url = :url");
		$this->core->db->bind_value(':url', $url, 'string');
		$this->core->db->execute();		
		if($this->core->db->prepNumRows()){
			return false;
		}else{
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
			return true;
		}
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
	public function edit_page($id, $application, $menutitle, $title, $url, $copy, $menushow)
	{
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
	 * Delete a page
	 * @param int $id
	 * @return boolean
	 */
	public function delete_page($id)
	{
		$this->core->db->prepare("DELETE FROM pages WHERE id = :id");
		$this->core->db->bind_value(':id', $id, 'int');
		return $this->core->db->execute();
	}
	
	/**
	 * Gets the URL for a specified ID
	 * @param int $id
	 * @return string|boolean
	 */
	public function get_url_from_id($id)
	{
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
	public function get_id_from_url($url)
	{
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
	public function get_order_from_id($id)
	{
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
	public function get_child_pages($id = false)
	{
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
	public function get_sibling_pages($id)
	{
		// Work out the page's parent
		$url = $this->get_url_from_id($id);
		$parenturl =  trim(implode('/',explode('/',$url,-1)));
	
		if(empty($parenturl)){
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
	public function page_order_up($id)
	{
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
	public function page_order_down($id)
	{
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
	
	/**
	 * Creates an assosiative array of the site structure
	 * @param string $parent
	 * @return array
	 */
	public function recursive_site_structure($parent = false)
	{
		if($parent){
			$folder = trim($parent, '/');
			$depth = substr_count($folder, '/');
			$depth++; //First level items would be counted as root otherwise
		}else{
			$depth = 0; //Root
		}
		if($parent){
			$this->core->db->prepare("SELECT url, menuTitle FROM pages
										WHERE menuShow = 1
											AND LENGTH(url) - LENGTH(REPLACE(url, '/', '')) = :depth
											AND url LIKE :parent
										ORDER BY ordering");
			$this->core->db->bind_value(':depth', $depth, 'int');
			$this->core->db->bind_value(':parent', $parent.'%', 'string');
	
		}else{
			// Get root pages
			$this->core->db->prepare("SELECT url, menuTitle FROM pages WHERE menuShow = 1 AND LENGTH(url) - LENGTH(REPLACE(url, '/', '')) = 0 ORDER BY ordering");
		}
		$this->core->db->execute();
		$children = $this->core->db->prepQueryAll();
	
		$output = array();
		foreach($children as $key => $child){
			$output[$child['menuTitle']] = $child;
			//If there are children, add them
			if($newchildren = $this->recursive_site_structure($child['url'])){
				$output[$child['menuTitle']]['children'] = $newchildren;
			}
		}
		return $output;
	}
	
	/**
	 * Get an array of the groups
	 * @return array
	 */
	public function get_groups(){
		return $this->core->db->queryAll("SELECT * FROM groups");
	}
	
	/**
	 * Get the permissions for a given page
	 * @param int $id
	 */
	public function get_page_permissions($id){
		$this->core->db->prepare("SELECT groups.displayName, groups.teamID, permissions_pages_link.allow
									FROM permissions_pages_link 
									LEFT JOIN groups ON groups.teamID = permissions_pages_link.teamID
									WHERE permissions_pages_link.teamID IN(SELECT groups.teamID FROM groups)
										AND permissions_pages_link.pageID = :id");
		$this->core->db->bind_value(':id', $id, 'int');
		$this->core->db->execute();
		if($groups = $this->core->db->prepQueryAll()){
			return $groups;
		}else{
			$groups = $this->get_groups();
			foreach($groups as $key => $group){
				$groups[$key]['allow'] = 1;
			}
			return $groups;
		}
	}
	
	public function save_permissions($pageID, $settings){
		// Build an array of the group IDs and the desired setting
		$groups = $this->get_groups();
		$array = array();
		foreach($groups as $group){
			$array[$group['teamID']] = false;
			foreach($settings as $setting){
				if($group['teamID'] == $setting){
					$array[$group['teamID']] = true;
					break;
				}				
			}
		}
		foreach($array as $teamID => $allow){
			$this->save_permission($pageID, $teamID, $allow);
		}		
	}
	
	public function save_permission($pageID, $teamID, $allow){
		if($allow){
			$allow = 1;
		}else{
			$allow = 0;
		}
		
		$this->core->db->prepare("SELECT id FROM permissions_pages_link WHERE pageID = :pageID AND teamID = :teamID");
		$this->core->db->bind_value(':pageID', $pageID, 'int');
		$this->core->db->bind_value(':teamID', $teamID, 'int');
		$this->core->db->execute();
		
		if($this->core->db->prepNumRows()){
			$this->core->db->prepare("UPDATE permissions_pages_link SET allow = :allow WHERE pageID = :pageID AND teamID = :teamID");
		}else{
			$this->core->db->prepare("INSERT INTO permissions_pages_link (pageID, teamID, allow) VALUES (:pageID, :teamID, :allow)");
		}
		
		$this->core->db->bind_value(':pageID', $pageID, 'int');
		$this->core->db->bind_value(':teamID', $teamID, 'int');
		$this->core->db->bind_value(':allow', $allow, 'int');
		return $this->core->db->execute();
	}
	
}

?>