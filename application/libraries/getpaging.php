<?php
class GetPaging {
	
	private $CI;
	private $base_url = '';
	private $per_page = 20;
	private $total_rows = 0;
	private $num_links			=  3; // Number of "digit" links to show before/after the currently viewed page
	private $cur_page	 		=  0; // The current page being viewed
	private $first_link   		= '&lsaquo; First Page';
	private $next_link			= 'Next &gt;';
	private $prev_link			= '&lt; Previous';
	private $last_link			= 'Last Page &rsaquo;';
	private $full_tag_open		= '';
	private $full_tag_close		= '';
	private $first_tag_open		= '';
	private $first_tag_close	= '&nbsp;';
	private $last_tag_open		= '&nbsp;';
	private $last_tag_close		= '';
	private $cur_tag_open		= '&nbsp;<span class="">';
	private $cur_tag_close		= '</span>';
	private $next_tag_open		= '&nbsp;';
	private $next_tag_close		= '&nbsp;';
	private $prev_tag_open		= '&nbsp;';
	private $prev_tag_close		= '';
	private $num_tag_open		= '&nbsp;';
	private $num_tag_close		= '';
	
	
	public function GetPaging($params = false) {
		$this->CI =& get_instance();
		if ($params) $this->initialise($params);
		
		//reconstruct get array!
		$query_string = urldecode($_SERVER["QUERY_STRING"]);
		
		$_SERVER['QUERY_STRING'] = $query_string;
        $get_array = array();
        
        parse_str($query_string,$get_array);
        
        foreach($get_array as $key => $val) {
            $_GET[$key] = $this->CI->input->xss_clean($val);
            $_REQUEST[$key] = $this->CI->input->xss_clean($val);
        }
        
		$page = $this->CI->input->get('page');
        
        if ( $page ) { 
        	$this->start_row = (intval($page) - 1) * $this->per_page;
        	$this->cur_page = intval($page);
        } else {
        	$this->start_row = 0;
        	$this->cur_page = 1;
        }
	}
	
	public function getStartRow() {
		return $this->start_row;
	}
	
	public function initialize($params) {
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}		
		}
	}
	
	public function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
		   return '';
		}

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
			return '';
		}

		$this->num_links = (int)$this->num_links;
		
		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}
				
		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = 0;
		}
		
		
		
		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;
		//$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

		//echo $this->cur_page;

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		// Add a trailing slash to the base URL if needed
		$this->base_url = rtrim($this->base_url, '/') .'/';

  		// And here we go...
		$output = '';

		// Render the "First" link
		if  ($this->cur_page > $this->num_links)
		{
			$output .= $this->first_tag_open.'<a class="first" href="'.$this->base_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "previous" link
		if  ($this->cur_page != 1)
		{
			$i = $uri_page_number - $this->per_page;
			if ($i == 0) $i = '';
			$output .= $this->prev_tag_open.'<a class="previous" href="'.$this->base_url.'?page='.($this->cur_page - 1).'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		}

		// Write the digit links
		for ($loop = $start -1; $loop <= $end; $loop++)
		{
			$i = ($loop * $this->per_page) - $this->per_page;
			
			
			
			if ($i >= 0)
			{
				if ($this->cur_page == $loop)
				{
					$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
				}
				else
				{
					$n = ($i == 0) ? '' : $i;
					$output .= $this->num_tag_open.'<a href="'.$this->base_url.'?page='.$loop.'">'.$loop.'</a>'.$this->num_tag_close;
				}
			}
		}

		// Render the "next" link
		if ($this->cur_page < $num_pages)
		{
			$output .= $this->next_tag_open.'<a class="next" href="'.$this->base_url.'?page='.($this->cur_page+1).'">'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link
		if (($this->cur_page + $this->num_links) < $num_pages)
		{
			$i = (($num_pages * $this->per_page) - $this->per_page);
			$output .= $this->last_tag_open.'<a class="last" href="'.$this->base_url.'?page='.$num_pages.'">'.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Kill double slashes.  Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;
		
		return $output;		
	}
	
}
?>