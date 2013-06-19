<?php
/**
* Based on the original library
*
* @autor: Alexander Rust
* @email: agrust@hotmail.com
* @date:  May 2009
*/

class MY_Table extends CI_Table {

  var $footing      = array();
  var $subheading   = array();

  public function __construct()
  {
      parent::__construct();
  }

  /**
   * Set the table footing. Similar to heading
   *
   * Can be passed as an array or discreet params
   *
   * @access  public
   * @param  mixed
   * @return  void
   */
  function set_footing()
  {
    $args = func_get_args();
	  $this->footing = $this->_prep_args($args);
  }

  // --------------------------------------------------------------------

  /**
   * Set the table sub headers. Similar to heading
   *
   * Can be passed as an array or discreet params
   *
   * @access  public
   * @param  mixed
   * @return  void
   */
  function set_subheading()
  {
    $args = func_get_args();
	  $this->subheading = $this->_prep_args($args);
  }

  // --------------------------------------------------------------------

  /**
   * Generate the table.
   *
   *
   * @access  public
   * @param  mixed
   * @return  string
   */
	function generate($table_data = NULL)
	{
		// The table data can optionally be passed to this function
		// either as a database result object or an array
		if ( ! is_null($table_data))
		{
			if (is_object($table_data))
			{
				$this->_set_from_object($table_data);
			}
			elseif (is_array($table_data))
			{
				$set_heading = (count($this->heading) == 0 AND $this->auto_heading == FALSE) ? FALSE : TRUE;
				$this->_set_from_array($table_data, $set_heading);
			}
		}

		// Is there anything to display?  No?  Smite them!
		if (count($this->heading) == 0 AND count($this->rows) == 0)
		{
			return 'Undefined table data';
		}

		// Compile and validate the template date
		$this->_compile_template();

		// set a custom cell manipulation function to a locally scoped variable so its callable
		$function = $this->function;

		// Build the table!

		$out = $this->template['table_open'];
		$out .= $this->newline;

		// Add any caption here
		if ($this->caption)
		{
			$out .= $this->newline;
			$out .= '<caption>' . $this->caption . '</caption>';
			$out .= $this->newline;
		}

		// Is there a table heading to display?
		if (count($this->heading) > 0)
		{
			$out .= $this->template['thead_open'];
			$out .= $this->newline;
			$out .= $this->template['heading_row_start'];
			$out .= $this->newline;

			foreach ($this->heading as $heading)
			{
				$temp = $this->template['heading_cell_start'];

				foreach ($heading as $key => $val)
				{
					if ($key != 'data')
					{
						$temp = str_replace('<th', "<th $key='$val'", $temp);
					}
				}

				$out .= $temp;
				$out .= isset($heading['data']) ? $heading['data'] : '';
				$out .= $this->template['heading_cell_end'];
			}

			$out .= $this->template['heading_row_end'];
			$out .= $this->newline;
			$out .= $this->template['thead_close'];
			$out .= $this->newline;
		}


		// Is there a table footing to display?
		if (count($this->footing) > 0)
		{
			$out .= $this->template['tfoot_open'];
			$out .= $this->newline;
			$out .= $this->template['footing_row_start'];
			$out .= $this->newline;

			foreach ($this->footing as $footing)
			{
				$temp = $this->template['footing_cell_start'];

				foreach ($footing as $key => $val)
				{
					if ($key != 'data')
					{
						$temp = str_replace('<td', "<td $key='$val'", $temp);
					}
				}

				$out .= $temp;
				$out .= isset($footing['data']) ? $footing['data'] : '';
				$out .= $this->template['footing_cell_end'];
			}

			$out .= $this->template['footing_row_end'];
			$out .= $this->newline;
			$out .= $this->template['tfoot_close'];
			$out .= $this->newline;
		}

		// Build the table rows
		if (count($this->rows) > 0)
		{
			$out .= $this->template['tbody_open'];
			$out .= $this->newline;
			
			// Is there a table subheading to display?
			if (count($this->subheading) > 0)
			{
				$out .= $this->template['subheading_row_start'];
				$out .= $this->newline;

				foreach ($this->subheading as $subheading)
				{
					$temp = $this->template['subheading_cell_start'];

					foreach ($subheading as $key => $val)
					{
						if ($key != 'data')
						{
							$temp = str_replace('<th', "<th $key='$val'", $temp);
						}
					}

					$out .= $temp;
					$out .= isset($subheading['data']) ? $subheading['data'] : '';
					$out .= $this->template['subheading_cell_end'];
				}

				$out .= $this->template['subheading_row_end'];
				$out .= $this->newline;
			}

			$i = 1;
			foreach ($this->rows as $row)
			{
				if ( ! is_array($row))
				{
					break;
				}

				// We use modulus to alternate the row colors
				$name = (fmod($i++, 2)) ? '' : 'alt_';

				$out .= $this->template['row_'.$name.'start'];
				$out .= $this->newline;

				foreach ($row as $cell)
				{
					$temp = $this->template['cell_'.$name.'start'];

					foreach ($cell as $key => $val)
					{
						if ($key != 'data')
						{
							$temp = str_replace('<td', "<td $key='$val'", $temp);
						}
					}

					$cell = isset($cell['data']) ? $cell['data'] : '';
					$out .= $temp;

					if ($cell === "" OR $cell === NULL)
					{
						$out .= $this->empty_cells;
					}
					else
					{
						if ($function !== FALSE && is_callable($function))
						{
							$out .= call_user_func($function, $cell);
						}
						else
						{
							$out .= $cell;
						}
					}

					$out .= $this->template['cell_'.$name.'end'];
				}

				$out .= $this->template['row_'.$name.'end'];
				$out .= $this->newline;
			}

			$out .= $this->template['tbody_close'];
			$out .= $this->newline;
		}

		$out .= $this->template['table_close'];

		// Clear table class properties before generating the table
		$this->clear();

		return $out;
	}


  function old_generate($table_data = NULL)
  {
    // The table data can optionally be passed to this function
    // either as a database result object or an array
    if ( ! is_null($table_data))
    {
      if (is_object($table_data))
      {
        $this->_set_from_object($table_data);
      }
      elseif (is_array($table_data))
      {
        $set_heading = (count($this->heading) == 0 AND $this->auto_heading == FALSE) ? FALSE : TRUE;
        $this->_set_from_array($table_data, $set_heading);
      }
    }

    // Is there anything to display?  No?  Smite them!
    if (count($this->heading) == 0 AND count($this->rows) == 0)
    {
      return 'Undefined table data';
    }

    // Compile and validate the template date
    $this->_compile_template();

    // Build the table!

    $out = $this->template['table_open'];
    $out .= $this->newline;

    // Add any caption here
    if ($this->caption)
    {
      $out .= $this->newline;
      $out .= '<caption>' . $this->caption . '</caption>';
      $out .= $this->newline;
    }
    // Is there a table heading to display?
    if (count($this->heading) > 0)
    {
      $out .= "<!-- Heading -->";
      $out .= $this->newline;
      $out .= $this->template['heading_open'];
      $out .= $this->newline;
      $out .= $this->template['heading_row_start'];
      $out .= $this->newline;

      foreach($this->heading as $heading)
      {
        $out .= $this->template['heading_cell_start'];
        $out .= $heading;
        $out .= $this->template['heading_cell_end'];
      }

      $out .= $this->template['heading_row_end'];
      $out .= $this->newline;
      $out .= $this->template['heading_close'];
      $out .= $this->newline;
    }

    // Are there subheadings or rows to display?
    if (count($this->subheading) > 0 OR count($this->rows) > 0) {
      // start the tbody
      $out .= $this->template['body_open'];
      $out .= $this->newline;

      // Is there a table heading to display?
      if (count($this->subheading) > 0)
      {
        $out .= "<!-- Sub Heading -->";
        $out .= $this->newline;
        $out .= $this->template['sub_heading_row_start'];
        $out .= $this->newline;

        foreach($this->subheading as $subheading)
        {
          $out .= $this->template['sub_heading_cell_start'];
          $out .= $subheading;
          $out .= $this->template['sub_heading_cell_end'];
        }

        $out .= $this->template['sub_heading_row_end'];
        $out .= $this->newline;
      }

      // Build the table rows
      if (count($this->rows) > 0)
      {
        $out .= "<!-- Table Rows -->";
        $out .= $this->newline;
        $i = 1;
        foreach($this->rows as $row)
        {
          if ( ! is_array($row))
          {
            break;
          }

          // We use modulus to alternate the row colors
          $name = (fmod($i++, 2)) ? '' : 'alt_';

          $out .= $this->template['row_'.$name.'start'];
          $out .= $this->newline;

          foreach($row as $cell)
          {
            $out .= $this->template['cell_'.$name.'start'];

            if ($cell === "")
            {
              $out .= $this->empty_cells;
            }
            else
            {
              $out .= $cell;
            }

            $out .= $this->template['cell_'.$name.'end'];
          }

          $out .= $this->template['row_'.$name.'end'];
          $out .= $this->newline;
        }
      }
      // end the tbody
      $out .= $this->template['body_close'];
      $out .= $this->newline;
    }

    // Is there a table footing to display?
    if (count($this->footing) > 0)
    {
      $out .= "<!-- Footing -->";
      $out .= $this->newline;
      $out .= $this->template['footing_open'];
      $out .= $this->newline;
      $out .= $this->template['footing_row_start'];
      $out .= $this->newline;

      foreach($this->heading as $footing)
      {
        $out .= $this->template['footing_cell_start'];
        $out .= $footing;
        $out .= $this->template['footing_cell_end'];
      }

      $out .= $this->template['footing_row_end'];
      $out .= $this->newline;
      $out .= $this->template['footing_close'];
      $out .= $this->newline;
    }

    $out .= $this->template['table_close'];

    return $out;
  }

  // --------------------------------------------------------------------

  /**
   * Compile Template
   *
   * @access  private
   * @return  void
   */
   function _compile_template()
   {
     if ($this->template == NULL)
     {
       $this->template = $this->_default_template();
       return;
     }

    $this->temp = $this->_default_template();
    foreach (array_keys($this->temp) as $val)
    {
      if ( ! isset($this->template[$val]))
      {
        $this->template[$val] = $this->temp[$val];
      }
    }
   }

  // --------------------------------------------------------------------

  /**
   * Default Template
   *
   * @access  private
   * @return  void
   */
  function _default_template()
  {
	return  array (
					'table_open'			=> '<table border="0" cellpadding="4" cellspacing="0">',

					'thead_open'			=> '<thead>',
					'thead_close'			=> '</thead>',

					'heading_row_start'		=> '<tr>',
					'heading_row_end'		=> '</tr>',
					'heading_cell_start'	=> '<th>',
					'heading_cell_end'		=> '</th>',
					
					'subheading_row_start'       => '<thead>',
		            'subheading_row_end'         => '</thead>',
		            'subheading_cell_start'      => '<th>',
		            'subheading_cell_end'        => '</th>',

					'tbody_open'			=> '<tbody>',
					'tbody_close'			=> '</tbody>',

					'row_start'				=> '<tr>',
					'row_end'				=> '</tr>',
					'cell_start'			=> '<td>',
					'cell_end'				=> '</td>',

					'row_alt_start'		=> '<tr>',
					'row_alt_end'			=> '</tr>',
					'cell_alt_start'		=> '<td>',
					'cell_alt_end'			=> '</td>',
					
					'tfoot_open'    => '<tfoot>',
		            'tfoot_close'   => '</tfoot>',
		            'footing_row_start'   => '<tr>',
		            'footing_row_end'     => '</tr>',
		            'footing_cell_start'  => '<td>',
		            'footing_cell_end'    => '</td>',

					'table_close'			=> '</table>'
				);

  }

  // --------------------------------------------------------------------

}

/* End of file MY_Table.php */
/* Location: ./system/application/libraries/MY_Table.php */