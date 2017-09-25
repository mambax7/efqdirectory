<?php
// $Id: listing.php,v 1.1.0 2007/11/03 17:46:00 wtravel
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

/**
 * Class efqDataField
 * Manages operations for datafields
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class efqDataField extends XoopsObject
{

    /**
     * Constructor
     */
    public function __construct()
    {
        // class constructor;
    }
}

/**
 * Class efqDataFieldHandler
 * Manages database operations for data fields
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class efqDataFieldHandler extends XoopsObjectHandler
{
    public $errorhandler;

    public function __construct()
    {
        //Instantiate class
        global $eh;
        $this->db           = XoopsDatabaseFactory::getDatabaseConnection();
        $this->errorhandler = $eh;
    }

    /**
     * @param     $itemid
     * @param int $show
     * @param int $min
     * @return array
     */
    public function getDataFields($itemid, $show = 10, $min = 0)
    {
        $sql = 'SELECT DISTINCT t.dtypeid, t.title, t.section, t.icon, f.typeid, f.fieldtype, f.ext, t.options, d.itemid, d.value, d.customtitle, t.custom ';
        $sql .= 'FROM ' . $this->db->prefix('efqdiralpha1_item_x_cat') . ' ic, ' . $this->db->prefix('efqdiralpha1_dtypes_x_cat') . ' xc, ' . $this->db->prefix('efqdiralpha1_fieldtypes') . ' f, ' . $this->db->prefix('efqdiralpha1_dtypes') . ' t ';
        $sql .= 'LEFT JOIN ' . $this->db->prefix('efqdiralpha1_data') . ' d ON (t.dtypeid=d.dtypeid AND d.itemid=' . $itemid . ') ';
        $sql .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND t.activeyn='1' AND ic.itemid=" . $itemid . '';
        $data_result = $this->db->query($sql) or $this->errorhandler->show('0013');
        //$numrows = $this->db->getRowsNum($data_result);
        $arr = [];
        while (list($dtypeid, $title, $section, $icon, $ftypeid, $fieldtype, $ext, $options, $itemid, $value, $customtitle, $custom) = $this->db->fetchRow($data_result)) {
            $fieldvalue = $this->getFieldValue($fieldtype, $options, $value);
            if ('' != $icon) {
                $iconurl = "<img src=\"uploads/$icon\" />";
            } else {
                $iconurl = '';
            }
            if ('0' != $custom && '' != $customtitle) {
                $title = $customtitle;
            }
            $arr[] = [
                'dtypeid'     => $dtypeid,
                'title'       => $title,
                'section'     => $section,
                'icon'        => $iconurl,
                'ftypeid'     => $ftypeid,
                'fieldtype'   => $fieldtype,
                'ext'         => $ext,
                'options'     => $options,
                'custom'      => $custom,
                'itemid'      => $itemid,
                'value'       => $fieldvalue,
                'customtitle' => $customtitle
            ];
        }

        return $arr;
    }

    /**
     * @param string $fieldtype
     * @param string $options
     * @param int    $value
     * @return mixed|string
     */
    public function getFieldValue($fieldtype = '', $options = '', $value = 0)
    {
        global $myts, $moddir;
        switch ($fieldtype) {
            case 'dhtml':
                return $myts->displayTarea($value);
                break;
            //case "gmap":
            //			$gmapHandler = new efqGmapHandler();
            //			$gmap = new efqGmap();
            //			$gmap->setPointsJS($gmapHandler->getPointsJS($gmap));
            //			$gmap->generateMap();
            //			$ret = $gmap->showMap();
            //			unset($gmap);
            //			unset($gmapHandler);
            //return $myts->makeTboxData4Show($value);
            //break;
            case 'radio':
                return $myts->makeTboxData4Show($value);
                break;
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
                $rating = "<img src=\"$src\" />";

                return $rating;
                break;
            case 'select':
                return $myts->makeTboxData4Show($value);
                break;
            case 'textbox':
                return $myts->makeTboxData4Show($value);
                break;
            case 'url':
                $link = explode('|', $value);

                return '<a href="' . $myts->makeTboxData4Show($link[0]) . '" title="' . $myts->makeTboxData4Show($link[1]) . '">' . $myts->makeTboxData4Show($link[0]) . '</a>';
                break;
            case 'yesno':
                if ('1' == $value) {
                    return _YES;
                } else {
                    return _NO;
                }
                break;
            default:
                return $myts->makeTboxData4Show($value);
                break;
        }
    }
}
