<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package      efqdirectory
 * @since
 * @author       Martijn Hertog (aka wtravel)
 * @author       XOOPS Development Team,
 */

class XoopsFormImage extends XoopsFormElement
{
    /**
     * Options
     * @var array
     * @access private
     */
    public $_options  = array();
    public $_multiple = false;
    public $_size;

    public $_height;
    public $_width;
    public $_src;
    public $_value = array();

    /**
     * Constructor
     *
     * @param string     $caption  Caption
     * @param string     $name     "name" attribute
     * @param mixed      $value    Pre-selected value (or array of them).
     * @param string     $src
     * @param string     $height
     * @param string     $width
     * @param bool       $multiple Allow multiple selections?
     * @param int|string $size     Size of the selection box
     */
    public function __construct($caption, $name, $value = null, $src = '', $height = '50', $width = '50', $multiple = false, $size = '1')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_multiple = $multiple;
        $this->_size     = (int)$size;
        if (isset($value)) {
            $this->setValue($value);
        }
        $this->_height = $height;
        $this->_width  = $width;
        $this->_src    = $src;
    }

    public function getWidth()
    {
        return $this->_width;
    }

    public function getHeight()
    {
        return $this->_height;
    }

    public function getSrc()
    {
        return $this->_src;
    }

    /*
    * Are multiple selections allowed?
    *
    * @return bool
    */
    public function isMultiple()
    {
        return $this->_multiple;
    }

    /**
     * Get the size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get an array of pre-selected values
     *
     * @return array
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set pre-selected values
     *
     * @param $value mixed
     */
    public function setValue($value)
    {
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
     * @param string $value "value" attribute
     * @param string $name  "name" attribute
     */
    public function addOption($value, $name = '')
    {
        if ($name != '') {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }

    /**
     * Add multiple options
     *
     * @param array $options Associative array of value->name pairs
     */
    public function addOptionArray($options)
    {
        if (is_array($options)) {
            foreach ($options as $k => $v) {
                $this->addOption($k, $v);
            }
        }
    }

    /**
     * Get all options
     *
     * @return array   Associative array of value->name pairs
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Prepare HTML for output
     *
     * @return string  HTML
     */
    public function render()
    {
        if ($this->getSrc() != '') {
            $ret = "<img src='" . $this->getSrc() . '\' width=\'' . $this->getWidth() . '\'  height=\'' . $this->getHeight() . '\'';
            $ret .= '>';
        } else {
            $ret = _MD_NOIMAGE;
        }

        return $ret;
    }
}
