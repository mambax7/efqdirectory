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
class efqDataFieldManager extends XoopsFormElement
{
    public $_options  = [];
    public $_multiple = false;
    public $_size;

    public $_height;
    public $_width;
    public $_src;
    public $_value = [];

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @param string $title
     * @param string $name
     * @param string $fieldtype
     * @param string $ext
     * @param string $options
     * @param string $value
     * @param string $custom
     * @param null   $customtitle
     */
    public function createField($title = '', $name = '', $fieldtype = '', $ext = '', $options = '', $value = '', $custom = '0', $customtitle = null)
    {
        global $form, $myts, $moddir;
        if (null == $customtitle) {
            $customtitle = '';
        }
        $multiple = false;
        if ('' != $ext) {
            $ext_arr = explode('[|]', $ext);
            foreach ($ext_arr as $ext_item) {
                $ext_item_arr   = explode('[=]', $ext_item);
                $ext_item_name  = $ext_item_arr[0];
                $ext_item_value = $ext_item_arr[1];

                switch ($ext_item_name) {
                    case 'cols':
                        $cols = $ext_item_value;
                        break;
                    case 'rows':
                        $rows = $ext_item_value;
                        break;
                    case 'size':
                        $size = $ext_item_value;
                        break;
                    case 'maxsize':
                        $maxsize = $ext_item_value;
                        break;
                    case 'multiple':
                        $multiple = true;
                        $size     = 5;
                    // no break
                    case 'value':
                        if ('' != $ext_item_value and '' == $value) {
                            $value = $ext_item_value;
                        }
                        break;
                }
            }
        }

        switch ($fieldtype) {
            case 'textbox':
                if ('1' == $custom) {
                    $form_text_tray = new XoopsFormElementTray($title, '', $name);
                    $form_text_tray->addElement(new XoopsFormLabel('', '<table><tr><td>'));
                    $form_text_tray->addElement(new XoopsFormText('<b>' . _MD_CUSTOM_TITLE . '</b></td><td>', 'custom' . $name, 50, 250, $customtitle));
                    $form_text_tray->addElement(new XoopsFormLabel('', '</td></tr><tr><td>'));
                    $form_text_tray->addElement(new XoopsFormText('<b>' . _MD_CUSTOM_VALUE . '</b></td><td>', $name, $size, $maxsize, $value));
                    $form_text_tray->addElement(new XoopsFormLabel('', '</td></tr></table>'));
                    $form->addElement($form_text_tray);
                } else {
                    $form->addElement(new XoopsFormText($title, $name, 50, 250, $myts->htmlSpecialChars($value)));
                }
                break;
            case 'textarea':
                if ('1' == $custom) {
                    $form_textarea_tray = new XoopsFormElementTray($title, '', $name);
                    $form_textarea_tray->addElement(new XoopsFormLabel('', '<table><tr><td>'));
                    $form_textarea_tray->addElement(new XoopsFormText('<b>' . _MD_CUSTOM_TITLE . '</b></td><td>', 'custom' . $name, 50, 250, $customtitle));
                    $form_textarea_tray->addElement(new XoopsFormLabel('', '</td></tr><tr><td>'));
                    $form_textarea_tray->addElement(new XoopsFormTextArea('<b>' . _MD_CUSTOM_VALUE . '</b></td><td>', $name, $value, $rows, $cols));
                    $form_textarea_tray->addElement(new XoopsFormLabel('', '</td></tr></table>'));
                    $form->addElement($form_textarea_tray);
                } else {
                    $form->addElement(new XoopsFormTextArea($title, $name, $value, $rows, $cols));
                }
                break;
            case 'yesno':
                $form->addElement(new XoopsFormRadioyn($title, $name, $value, _YES, _NO));
                break;
            case 'radio':
                $options_arr = explode('[|]', $options);
                $form_radio  = new XoopsFormRadio($title, $name, $value, 1);
                foreach ($options_arr as $option) {
                    $form_radio->addOption($option, $option);
                }
                $form->addElement($form_radio);
                break;
            case 'checkbox':
                $options_arr   = explode('[|]', $options);
                $form_checkbox = new XoopsFormCheckbox($title, $name, $value, 1);
                foreach ($options_arr as $option) {
                    $form_checkbox->addOption($option, $option);
                }
                $form->addElement($form_checkbox);
                break;
            case 'select':
                $options_arr = explode('[|]', $options);
                $value_arr   = explode('[|]', $value);
                $form_select = new XoopsFormSelect($title, $name, $value, $size, $multiple);
                $form_select->addOption('-', '----');
                $form_select->setValue($value_arr);
                foreach ($options_arr as $key => $option) {
                    $form_select->addOption($option, $option);
                }
                $form->addElement($form_select);
                break;
            case 'dhtml':
                if ('1' == $custom) {
                    $form_dhtmlarea_tray = new XoopsFormElementTray($title, '', $name);
                    $form_dhtmlarea_tray->addElement(new XoopsFormLabel('', '<table><tr><td>'));
                    $form_dhtmlarea_tray->addElement(new XoopsFormText('<b>' . _MD_CUSTOM_TITLE . '</b></td><td>', 'custom' . $name, 50, 250, $customtitle));
                    $form_dhtmlarea_tray->addElement(new XoopsFormLabel('', '</td></tr><tr><td>'));
                    $form_dhtmlarea_tray->addElement(new XoopsFormDhtmlTextArea('<b>' . _MD_CUSTOM_VALUE . '</b></td><td>', $name, $value, $rows, $cols));
                    $form_dhtmlarea_tray->addElement(new XoopsFormLabel('', '</td></tr></table>'));
                    $form->addElement($form_dhtmlarea_tray);
                } else {
                    $form->addElement(new XoopsFormDhtmlTextArea($title, $name, $value, $rows, $cols));
                }
                break;
            case 'address':
                //Query all address fields associated with the address type that belongs to the locid.
                $addressfields = getAddressFields('0');
                $addressvalues = getAddressValues($value);
                $form->addElement(new XoopsFormLabel('', '<strong>' . $title . '</strong>'));
                $fieldtitles = [
                    'address'  => _MD_DF_ADDRESS,
                    'address2' => _MD_DF_ADDRESS2,
                    'zip'      => _MD_DF_ZIP,
                    'postcode' => _MD_DF_POSTCODE,
                    'lat'      => _MD_DF_LAT,
                    'lon'      => _MD_DF_LON,
                    'phone'    => _MD_DF_PHONE,
                    'fax'      => _MD_DF_FAX,
                    'mobile'   => _MD_DF_MOBILE,
                    'city'     => _MD_DF_CITY,
                    'country'  => _MD_DF_COUNTRY,
                    'typename' => _MD_DF_TYPENAME,
                    'uselocyn' => _MD_DF_USELOCYN
                ];
                foreach ($addressfields['addressfields'] as $field => $fieldvalue) {
                    $storedvalue = $addressvalues["$field"];
                    if (1 == $fieldvalue) {
                        $title = $fieldtitles["$field"];
                        $form->addElement(new XoopsFormText($title, $name . $field, 50, 250, $myts->htmlSpecialChars($storedvalue)));
                    }
                }
                $form->addElement(new XoopsFormHidden('submitaddress', '1'));
                $form->addElement(new XoopsFormHidden($name, $value));
                $form->addElement(new XoopsFormHidden('addrid', $value));
                break;
            case 'rating':
                $rating_options = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
                $form_rating    = new XoopsFormSelect($title, $name, $value, 1);
                $form_rating->addOption('0', '----');
                foreach ($rating_options as $option) {
                    $form_rating->addOption($option, $option);
                }
                $form->addElement($form_rating);
                break;
            case 'date':
                $form->addElement(new XoopsFormText($title, $name, 10, 10, $value));
                break;
            case 'url':
                if ('' != $value) {
                    $link = explode('|', $value);
                } else {
                    $link    = [];
                    $link[0] = '';
                    $link[1] = '';
                }
                $form_textarea_tray = new XoopsFormElementTray($title, '', $name);
                $form_textarea_tray->addElement(new XoopsFormLabel('', '<table><tr><td>'));
                $form_textarea_tray->addElement(new XoopsFormText('<b>' . _MD_FIELDNAMES_URL_TITLE . '</b></td><td>', 'url_title' . $name, 50, 250, $link[1]));
                $form_textarea_tray->addElement(new XoopsFormLabel('', '</td></tr><tr><td>'));
                $form_textarea_tray->addElement(new XoopsFormText('<b>' . _MD_FIELDNAMES_URL_LINK . '</b></td><td>', 'url_link' . $name, 50, 250, $link[0]));
                $form_textarea_tray->addElement(new XoopsFormLabel('', '</td></tr></table>'));
                $form->addElement($form_textarea_tray);
                break;
            default:
                echo $fieldtype . ' is an unknown field type.';
                break;
        }
    }

    /**
     * @param string $fieldtype
     * @param string $options
     * @param int    $value
     * @return mixed|string
     */
    public function getFieldValue($fieldtype = '', $options = '', $value = 0)
    {
        global $form, $myts, $moddir;
        switch ($fieldtype) {
            case 'textbox':
                return $myts->htmlSpecialChars($value);
                break;
            case 'yesno':
                if ('1' == $value) {
                    return _YES;
                } else {
                    return _NO;
                }
                break;
            case 'radio':
                return $myts->htmlSpecialChars($value);
                break;
            case 'select':
                return $myts->htmlSpecialChars($value);
            case 'dhtml':
                return $myts->displayTarea($value);
            case 'rating':
                $xoops_url = XOOPS_URL;
                switch ($value) {
                    case 1:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_1.gif";
                        break;
                    case 2:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_2.gif";
                        break;
                    case 3:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_3.gif";
                        break;
                    case 4:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_4.gif";
                        break;
                    case 5:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_5.gif";
                        break;
                    case 6:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_6.gif";
                        break;
                    case 7:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_7.gif";
                        break;
                    case 8:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_8.gif";
                        break;
                    case 9:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_9.gif";
                        break;
                    case 10:
                        $src = "$xoops_url/modules/$moddir/assets/images/rating_10.gif";
                        break;
                    default:
                        $src = '';
                }
                $rating = "<img src=\"$src\">";

                return $rating;
            case 'address':
                $fieldtitles   = [
                    'address'  => _MD_DF_ADDRESS,
                    'address2' => _MD_DF_ADDRESS2,
                    'zip'      => _MD_DF_ZIP,
                    'postcode' => _MD_DF_POSTCODE,
                    'lat'      => _MD_DF_LAT,
                    'lon'      => _MD_DF_LON,
                    'phone'    => _MD_DF_PHONE,
                    'fax'      => _MD_DF_FAX,
                    'mobile'   => _MD_DF_MOBILE,
                    'city'     => _MD_DF_CITY,
                    'country'  => _MD_DF_COUNTRY
                ];
                $addressfields = getAddressFields('0');
                $addressvalues = getAddressValues($value);
                $address       = '';

                foreach ($addressfields['addressfields'] as $field => $fieldvalue) {
                    $storedvalue = $addressvalues["$field"];
                    if ('1' == $fieldvalue && '' != $storedvalue) {
                        $title = $fieldtitles["$field"];

                        switch ($field) {
                            case 'address':
                                $street = $myts->htmlSpecialChars($storedvalue);
                                break;
                            case 'city':
                                $city = $myts->htmlSpecialChars($storedvalue);
                                break;
                            case 'zip':
                                $zip = $myts->htmlSpecialChars($storedvalue);
                                break;
                            case 'state':
                                $state = $myts->htmlSpecialChars($storedvalue);
                                break;
                            case 'country':
                                $country = $myts->htmlSpecialChars($storedvalue);
                                break;
                            default:
                                break;
                        }
                        $address .= $myts->htmlSpecialChars($storedvalue) . '<br>';
                    }
                }
                if (!isset($street)) {
                    $street = '';
                }
                if (!isset($city)) {
                    $city = '';
                }
                if (!isset($zip)) {
                    $zip = '';
                }
                if (!isset($state)) {
                    $state = '';
                }
                if (!isset($country)) {
                    $country = '';
                }
                if ('United States' === $country) {
                    $countrycode = 'us';
                } elseif ('Canada' === $country) {
                    $countrycode = 'ca';
                }
                if (isset($countrycode) && isset($city) && isset($street)) {
                    $address .= '<form name=mapForm2 action="http://us.rd.yahoo.com/maps/home/submit_a/*-http://maps.yahoo.com/maps" target="_new" method=get>
                    <input type="hidden" name="addr" value="' . $street . '">
                    <input type="hidden" name="csz" value="' . $city . ', ' . $state . ' ' . $zip . '">
                    <input type="hidden" name="country" value="' . $countrycode . '">
                    <input type=hidden name=srchtype value=a>
                    <input type=submit name="getmap" value="Yahoo Map">
                    </form>';
                }

                return $address;
            case 'url':
                $link = explode('|', $value);

                return '<a href="' . $myts->htmlSpecialChars($link[0]) . '" title="' . $myts->htmlSpecialChars($link[1]) . '">' . $myts->htmlSpecialChars($link[0]) . '</a>';
                break;
            default:
                return $myts->htmlSpecialChars($value);
                break;
        }
    }

    /**
     * @param string $title
     * @param string $name
     * @param string $fieldtype
     * @param string $ext
     * @param string $options
     * @param string $value
     * @param string $custom
     * @param null   $customtitle
     */
    public function createSearchField($title = '', $name = '', $fieldtype = '', $ext = '', $options = '', $value = '', $custom = '0', $customtitle = null)
    {
        global $form, $myts;

        switch ($fieldtype) {
            case 'textbox':
                $this->createSearchField_text($title, $name, $value, $options);
                break;
            case 'textarea':
                $this->createSearchField_text($title, $value, $options);
                break;
            case 'yesno':
                $form->addElement(new XoopsFormRadioyn($title, $name, '', _YES, _NO));
                break;
            case 'radio':
                $this->createSearchField_select($title, $name, $value, $options);
                break;
            case 'checkbox':
                $this->createSearchField_checkbox($title, $name, $value, $options);
                break;
            case 'select':
                $this->createSearchField_checkbox($title, $name, $value, $options);
                break;
            case 'dhtml':
                $this->createSearchField_text($title, $name, $value, $options);
                break;
            case 'address':
                //Query all address fields associated with the address type that belongs to the locid.
                /* $addressfields = getAddressFields('0');
                $fieldtitles = array('address' => _MD_DF_ADDRESS, 'address2' => _MD_DF_ADDRESS2, 'zip' => _MD_DF_ZIP, 'postcode' => _MD_DF_POSTCODE, 'lat' => _MD_DF_LAT, 'lon' => _MD_DF_LON, 'phone' => _MD_DF_PHONE, 'fax' => _MD_DF_FAX, 'mobile' => _MD_DF_MOBILE, 'city' => _MD_DF_CITY, 'country' => _MD_DF_COUNTRY, 'typename' => _MD_DF_TYPENAME);
                $form->addElement(new XoopsFormLabel("", "<strong>".$title."</strong>"));
                foreach ($addressfields['addressfields'] as $field => $fieldvalue) {
                if ($fieldvalue == 1) {
                $title = $fieldtitles["$field"];
                $form->addElement(new XoopsFormText($title, $name.$field, 50, 250, ""));
                }
                }
                $form->addElement(new XoopsFormHidden("submitaddress","1"));
                $form->addElement(new XoopsFormHidden($name, $value));
                $form->addElement(new XoopsFormHidden("addrid", $value)); */
                break;
            case 'rating':
                $this->createSearchField_rating($title, $name, $value, $options);
                break;
            case 'date':
                $this->createSearchField_text($title, $name, $value, $options);
                break;
            case 'url':
                $this->createSearchField_text($title, $name, $value, $options);
            // no break
            default:
                echo $fieldtype . ' geen bekend veldtype ';
                break;
        }
    }

    /**
     * @param string $title
     * @param string $name
     * @param string $value
     * @param string $options
     */
    public function createSearchField_text($title = '', $name = '', $value = '', $options = '')
    {
        global $form, $myts;
        $options_arr_constr = [
            'equal'      => _MD_EQUAL_TO,
            'notequal'   => _MD_NOT_EQUAL_TO,
            'contains'   => _MD_CONTAINS,
            'begins'     => _MD_BEGINSWITH,
            'ends'       => _MD_ENDSWITH,
            'notcontain' => _MD_NOTCONTAIN
        ];
        $form_tray          = new XoopsFormElementTray($title, '', $name);
        $form_tray->addElement(new XoopsFormLabel('', '<table><tr><td width="150">'));
        $form_select_constr = new XoopsFormSelect('', $name . 'constr', $value, 1);
        //$form_select->addOption('0', '----');
        foreach ($options_arr_constr as $optionname => $option) {
            $form_select_constr->addOption($optionname, $option);
        }
        $form_tray->addElement($form_select_constr);
        $form_tray->addElement(new XoopsFormLabel('', '</td><td>'));
        $form_tray->addElement(new XoopsFormText('', $name, 50, 250, ''));
        $form_tray->addElement(new XoopsFormLabel('', '</td></tr></table>'));
        $form->addElement($form_tray);
    }

    /**
     * @param string $title
     * @param string $name
     * @param string $value
     * @param string $options
     */
    public function createSearchField_checkbox($title = '', $name = '', $value = '', $options = '')
    {
        global $form, $myts;
        $options_arr   = explode('[|]', $options);
        $countoptions  = count($options_arr) + 1;
        $form_checkbox = new XoopsFormCheckBox($title, $name, $value);
        foreach ($options_arr as $optionname) {
            $form_checkbox->addOption($optionname, $optionname);
        }
        $form->addElement($form_checkbox);
    }

    /**
     * @param string $title
     * @param string $name
     * @param string $value
     * @param string $options
     */
    public function createSearchField_select($title = '', $name = '', $value = '', $options = '')
    {
        global $form, $myts;
        $options_arr        = explode('[|]', $options);
        $options_arr_constr = ['equal' => _MD_EQUAL_TO, 'notequal' => _MD_NOT_EQUAL_TO];
        $form_tray          = new XoopsFormElementTray($title, '', $name);
        $form_tray->addElement(new XoopsFormLabel('', '<table><tr><td width="150">'));
        $form_select_constr = new XoopsFormSelect('', $name, $value, 1);
        foreach ($options_arr_constr as $option) {
            $form_select_constr->addOption($option, $option);
        }
        $form_tray->addElement($form_select_constr);
        $form_tray->addElement(new XoopsFormLabel('', '</td><td>'));
        $countoptions = count($options_arr) + 1;
        if ($countoptions >= 5) {
            $selectformsize = 5;
        } else {
            $selectformsize = $countoptions;
        }
        $form_select = new XoopsFormSelect('', $name, $value, $selectformsize);
        $form_select->setExtra(" multiple='multiple'");
        $form_select->addOption('0', '----');
        foreach ($options_arr as $option) {
            $form_select->addOption($option, $option);
        }
        $form_tray->addElement($form_select);
        $form_tray->addElement(new XoopsFormLabel('', '</td></tr></table>'));
        $form->addElement($form_tray);
    }

    /**
     * @param string $title
     * @param string $name
     * @param string $value
     * @param string $options
     */
    public function createSearchField_rating2($title = '', $name = '', $value = '', $options = '')
    {
        global $form, $myts;
        $options_arr    = explode('[|]', $options);
        $rating_options = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $form_rating    = new XoopsFormSelect($title, $name, $value, 1);
        $form_rating->addOption('0', '----');
        foreach ($rating_options as $option) {
            $form_rating->addOption($option, $option);
        }
        $form->addElement($form_rating);
    }

    /**
     * @param string $title
     * @param string $name
     * @param string $value
     * @param string $options
     */
    public function createSearchField_rating($title = '', $name = '', $value = '', $options = '')
    {
        global $form, $myts;
        $options_arr        = explode('[|]', $options);
        $rating_options     = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $options_arr_constr = [
            'equal'    => _MD_EQUAL_TO,
            'notequal' => _MD_NOT_EQUAL_TO,
            'smaller'  => _MD_SMALLER_THAN,
            'bigger'   => _MD_GREATER_THAN
        ];
        $form_tray          = new XoopsFormElementTray($title, '', $name);
        $form_tray->addElement(new XoopsFormLabel('', '<table><tr><td width="150">'));
        $form_select_constr = new XoopsFormSelect('', $name . 'constr', $value, 1);
        foreach ($options_arr_constr as $optionname => $option) {
            $form_select_constr->addOption($optionname, $option);
        }
        $form_tray->addElement($form_select_constr);
        $form_tray->addElement(new XoopsFormLabel('', '</td><td>'));
        $countoptions = count($rating_options) + 1;
        if ($countoptions >= 5) {
            $selectformsize = 5;
        } else {
            $selectformsize = $rating_options;
        }
        $form_select = new XoopsFormSelect('', $name, $value, $selectformsize);
        //$form_select->setExtra(" multiple='multiple'");
        $form_select->addOption('0', '----');
        foreach ($rating_options as $option) {
            $form_select->addOption($option, $option);
        }
        $form_tray->addElement($form_select);
        $form_tray->addElement(new XoopsFormLabel('', '</td></tr></table>'));
        $form->addElement($form_tray);
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * @return mixed
     */
    public function getSrc()
    {
        return $this->_src;
    }

    /*
    * Are multiple selections allowed?
    *
    * @return bool
    */
    /**
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
        if ('' != $name) {
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
        $ret = "<img src='" . $this->getSrc() . '\' width=\'' . $this->getWidth() . '\'  height=\'' . $this->getHeight() . '\'';
        $ret .= '>';

        return $ret;
    }
}
