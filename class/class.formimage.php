<?php
// $Id: datafieldmanager.php,v 0.18 2006/03/23 21:37:00 wtravel
//  ------------------------------------------------------------------------ //
//                				EFQ Directory			                     //
//                    Copyright (c) 2006 EFQ Consultancy                     //
//                       <http://www.efqdirectory.com/>                      //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
//	Part of the efqDirectory module provided by: wtravel					 //
// 	e-mail: info@efqdirectory.com											 //
//	Purpose: Create a business directory for xoops.		 	 				 //
//	Based upon the mylinks and the mxDirectory modules						 //
// ------------------------------------------------------------------------- //

class XoopsFormImage extends XoopsFormElement {

	/**
     * Options
	 * @var array   
	 * @access	private
	 */
	var $_options = array();
	var $_multiple = false;
	var $_size;
	
	var $_height;
	var $_width;
	var $_src;
	var $_value = array();

	/**
	 * Constructor
	 * 
	 * @param	string	$caption	Caption
	 * @param	string	$name       "name" attribute
	 * @param	mixed	$value	    Pre-selected value (or array of them).
	 * @param	int		$size	    Number or rows. "1" makes a drop-down-list
     * @param	bool    $multiple   Allow multiple selections?
     * @param	int    	$size   	Size of the selection box
	 */
	function XoopsFormImage($caption, $name, $value=null, $src="", $height="50", $width="50", $multiple=false, $size="1"){
		$this->setCaption($caption);
		$this->setName($name);
		$this->_multiple = $multiple;
		$this->_size = intval($size);
		if (isset($value)) {
			$this->setValue($value);
		}
		$this->_height = $height;
		$this->_width = $width;
		$this->_src = $src;
	}

	function getWidth(){
		return $this->_width;
	}
	function getHeight(){
		return $this->_height;
	}
	function getSrc(){
		return $this->_src;
	}
	/*
	 * Are multiple selections allowed?
	 * 
     * @return	bool
	 */
	function isMultiple(){
		return $this->_multiple;
	}

	/**
	 * Get the size
	 * 
     * @return	int
	 */
	function getSize(){
		return $this->_size;
	}

	/**
	 * Get an array of pre-selected values
	 * 
     * @return	array
	 */
	function getValue(){
		return $this->_value;
	}

	/**
	 * Set pre-selected values
	 * 
     * @param	$value	mixed
	 */
	function setValue($value){
		if (is_array($value)) {
			foreach ($value as $v) {
				$this->_value[] = $v;
			}
		} else {
			$this->_value[] = $value;
		}
	}

	/**
	 * Add an option
     * 
	 * @param	string  $value  "value" attribute
     * @param	string  $name   "name" attribute
	 */
	function addOption($value, $name=""){
		if ( $name != "" ) {
			$this->_options[$value] = $name;
		} else {
			$this->_options[$value] = $value;
		}
	}

	/**
	 * Add multiple options
	 * 
     * @param	array   $options    Associative array of value->name pairs
	 */
	function addOptionArray($options){
		if ( is_array($options) ) {
			foreach ( $options as $k=>$v ) {
				$this->addOption($k, $v);
			}
		}
	}

	/**
	 * Get all options
	 * 
     * @return	array   Associative array of value->name pairs
	 */
	function getOptions(){
		return $this->_options;
	}
	

	/**
	 * Prepare HTML for output
	 * 
     * @return	string  HTML
	 */
	function render(){
		if ($this->getSrc() != "") {
			$ret = "<img src='".$this->getSrc()."' width='".$this->getWidth()."'  height='".$this->getHeight()."'";
			$ret .= " />";
		} else {
			$ret = _MD_NOIMAGE;
		}
		return $ret;
	}
}
?>