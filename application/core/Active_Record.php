<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Custom Active Record
 *
 * Provides extensions to the Active Record library allowing grouped where clauses
 *
 * @package		CodeIgniter
 * @author		Harry Thomos - based on work of EllisLab, Inc.
 * @copyright	Copyright 2012, EllisLab, Inc.
 * @license		GPL
 * @link		http://www.webrevised.com/189-extending-code-igniter-active-record-class/
 */
class custom_active_record extends CI_DB_active_record
{
	/**
	 * Where
	 *
	 * Called by where() or or_where()
	 *
	 * @param	mixed
	 * @param	mixed
	 * @param	string
	 * @return	object
	 */
	 
	function group_start()
	{
		$this->ar_where[] = "(";		
		return $this;
	}
	function group_end()
	{
		$this->ar_where[] = ")";		
		return $this;
	}
	 
	function and_()
	{
		$this->ar_where[] = " AND ";
		return $this;
	}
	
	function or_()
	{	$this->ar_where[] = " OR " ;
		return $this;
	}
	 
	protected function _where($key, $value = NULL, $type = 'AND ', $escape = NULL)
	{
		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		// If the escape value was not set will will base it on the global setting
		if ( ! is_bool($escape))
		{
			$escape = $this->_protect_identifiers;
		}

		foreach ($key as $k => $v)
		{
		
			$prefix = (count($this->ar_where) == 0 AND count($this->ar_cache_where) == 0) ? '' : $type;
			if (count($this->ar_where)>0)
			{
				$item = array_pop($this->ar_where);
				if (($item=='(')||($item==')'))
					$prefix = "";
				$this->ar_where[] = $item;
			}

			if (is_null($v) && ! $this->_has_operator($k))
			{
				// value appears not to have been set, assign the test to IS NULL
				$k .= ' IS NULL';
			}

			if ( ! is_null($v))
			{
				if ($escape === TRUE)
				{
					$k = $this->_protect_identifiers($k, FALSE, $escape);

					$v = ' '.$this->escape($v);
				}
				
				if ( ! $this->_has_operator($k))
				{
					$k .= ' = ';
				}
			}
			else
			{
				$k = $this->_protect_identifiers($k, FALSE, $escape);
			}
			
			$this->ar_where[] = $prefix.$k.$v;

			if ($this->ar_caching === TRUE)
			{
				$this->ar_cache_where[] = $prefix.$k.$v;
				$this->ar_cache_exists[] = 'where';
			}

		}

		return $this;
	}
	
	
	/**
	 * Like
	 *
	 * Called by like() or orlike()
	 *
	 * @param	mixed
	 * @param	mixed
	 * @param	string
	 * @return	object
	 */
	protected function _like($field, $match = '', $type = 'AND ', $side = 'both', $not = '')
	{
		if ( ! is_array($field))
		{
			$field = array($field => $match);
		}

		foreach ($field as $k => $v)
		{
			$k = $this->_protect_identifiers($k);

			$prefix = (count($this->ar_where) == 0) ? '' : $type;
			if (count($this->ar_where)>0)
			{
				$item = array_pop($this->ar_where);
				if (($item=='(')||($item==')'))
					$prefix = "";
				$this->ar_where[] = $item;
			}

			$v = $this->escape_like_str($v);

			if ($side == 'before')
			{
				$like_statement = $prefix." $k $not LIKE '%{$v}'";
			}
			elseif ($side == 'after')
			{
				$like_statement = $prefix." $k $not LIKE '{$v}%'";
			}
			else
			{
				$like_statement = $prefix." $k $not LIKE '%{$v}%'";
			}

			// some platforms require an escape sequence definition for LIKE wildcards
			if ($this->_like_escape_str != '')
			{
				$like_statement = $like_statement.sprintf($this->_like_escape_str, $this->_like_escape_chr);
			}

			//$this->ar_like[] = $like_statement;
			$this->ar_where[] = $like_statement;
			if ($this->ar_caching === TRUE)
			{
				$this->ar_cache_where[] = $like_statement;
				$this->ar_cache_exists[] = 'where';
			}
			
			

		}
		return $this;
	}
	
	/**
	 * Where_in
	 *
	 * Called by where_in, where_in_or, where_not_in, where_not_in_or
	 *
	 * @param	string	The field to search
	 * @param	array	The values searched on
	 * @param	boolean	If the statement would be IN or NOT IN
	 * @param	string
	 * @return	object
	 */
	protected function _where_in($key = NULL, $values = NULL, $not = FALSE, $type = 'AND ')
	{
		if ($key === NULL OR $values === NULL)
		{
			return;
		}

		if ( ! is_array($values))
		{
			$values = array($values);
		}

		$not = ($not) ? ' NOT' : '';

		foreach ($values as $value)
		{
			$this->ar_wherein[] = $this->escape($value);
		}

		$prefix = (count($this->ar_where) == 0) ? '' : $type;

		//Fix the prefix for our group elements
		if (($prefix)&&(count($this->ar_where))&&($this->ar_where[count($this->ar_where)-1]=="("))
			$prefix = "";
		
		$where_in = $prefix . $this->_protect_identifiers($key) . $not . " IN (" . implode(", ", $this->ar_wherein) . ") ";

		$this->ar_where[] = $where_in;
		if ($this->ar_caching === TRUE)
		{
			$this->ar_cache_where[] = $where_in;
			$this->ar_cache_exists[] = 'where';
		}

		// reset the array for multiple calls
		$this->ar_wherein = array();
		return $this;
	}
	
}