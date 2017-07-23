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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS Root Path not defined');
/**
 *
 *
 * @package     kernel
 * @subpackage  form
 *
 * @author      Kazumi Ono <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A Group of radiobuttons
 *
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 *
 * @package    kernel
 * @subpackage form
 */
class efqFormRadio extends XoopsFormElement
{
    /**
     * Array of Options
     * @var array
     * @access private
     */
    public $_options = array();

    /**
     * Pre-selected value
     * @var string
     * @access private
     */
    public $_value;

    /**
     * Pre-selected value
     * @var string
     * @access private
     */
    public $_linebreak;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name    "name" attribute
     * @param string $value   Pre-selected value
     * @param null   $linebreak
     */
    public function __construct($caption, $name, $value = null, $linebreak = null)
    {
        $this->setCaption($caption);
        $this->setName($name);
        if (isset($value)) {
            $this->setValue($value);
        }
        if (isset($linebreak)) {
            $this->setLineBreak($linebreak);
        } else {
            $this->setLineBreak('');
        }
    }

    /**
     * Get the pre-selected value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Get the pre-selected value
     *
     * @return string
     */
    public function getLineBreak()
    {
        return $this->_linebreak;
    }

    /**
     * Set the pre-selected value
     *
     * @param $value string
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Set the pre-selected value
     *
     * @param $linebreak
     * @internal param string $value
     */
    public function setLineBreak($linebreak)
    {
        $this->_linebreak = $linebreak;
    }

    /**
     * Add an option
     *
     * @param string $value "value" attribute - This gets submitted as form-data.
     * @param string $name  "name" attribute - This is displayed. If empty, we use the "value" instead.
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
     * Adds multiple options
     *
     * @param array $options Associative array of value->name pairs.
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
     * Gets the options
     *
     * @return array Associative array of value->name pairs.
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        $ret = '';
        foreach ($this->getOptions() as $value => $name) {
            $ret      .= "<input type='radio' name='" . $this->getName() . '\' value=\'' . $value . '\'';
            $selected = $this->getValue();
            if (isset($selected) && ($value == $selected)) {
                $ret .= ' checked';
            }
            $ret .= $this->getExtra() . '>' . $name . '' . $this->getLineBreak() . "\n";
        }

        return $ret;
    }
}
