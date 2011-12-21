<?php

// Breadcrumb generator for CodeIgniter
class Breadcrumb
{
	// variables
	public $link_type = ' &gt; '; // must have spaces around it
	public $breadcrumb = array();
	public $output = '';
	public $prefix = '<div id="breadcrumb">';
	public $suffix = '</div>';
	
	// clear
	public function clear()
	{
		// clear the breadcrumb library to start again
		$props = array('breadcrumb', 'output');
		
		// loop through
		foreach($props as $val)
		{
			// clear
			$this->$val = null;
		}
		
		// completed
		return true;
	}
	
	// add a "crumb"
	public function add_crumb($title, $url = false)
	{
		// pass into breadcrumb array
		$this->breadcrumb[] = array('title' => $title,
									'url' => $url);
										   
		// completed
		return true;
	}
	
	// change link type
	public function change_link($new_link)
	{
		// change
		$this->link_type = ' ' . $new_link . ' '; // the spaces are added for visual reasons
		
		// completed
		return true;
	}
	
	// produce output
	public function output()
	{
		// define local counter
		$counter = 0;
		
		// loop through breadcrumbs
		foreach($this->breadcrumb as $key=>$val)
		{
			// do we need to add a link?
			if($counter > 0)
			{
				// we do
				$this->output .= $this->link_type;
			}
			
			
			
			// are we using a hyperlink?
			if($val['url'] && (count($this->breadcrumb) > $counter+1))
			{
				// add href tag
				//$this->output .= '<a href="' . $val['url'] . '">' . $val['title'] . '</a>';
				$this->output .= anchor($val['url'],$val['title']);
			} else {
				// don't use hyperlinks
				$this->output .= $val['title'];
			}
			
			// increment counter
			$counter++;
		}
		
		// return
		if(count($this->breadcrumb) > 0){
			return $this->prefix.$this->output.$this->suffix;
		}else{
			return '';
		}
	}
}