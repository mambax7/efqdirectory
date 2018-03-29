<?php namespace XoopsModules\Efqdirectory;

/*
// ID: category.php 3-nov-2007 18:18:06 efqconsultancy
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
*/

use XoopsModules\Efqdirectory;

/**
 * Class Gmap
 * Manages operations for google maps
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class Gmap extends \XoopsObject
{
    private $_lon; //longitude
    private $_lat; //lattitude
    private $_descr; //description
    private $_key; //developer's key
    private $_zoomlevel; //zoom level of google map
    private $_jsPointsArray; //javascript assigning points to google map
    private $_map; // generate output for showing the map on a web page
    public $db;

    /**
     * Gmap constructor.
     * @param bool $gmap
     */
    public function __construct($gmap = false)
    {
        /** @var Efqdirectory\Helper $helper */
        $helper = Efqdirectory\Helper::getInstance();
        $key = $helper->getConfig('gmapkey');
        $this->setKey($key);
        $this->setPointsJS('');

        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('dataid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('lat', XOBJ_DTYPE_TXTBOX, null, true);
        $this->initVar('lon', XOBJ_DTYPE_TXTBOX, null, true);
        $this->initVar('descr', XOBJ_DTYPE_TXTAREA);

        if (false !== $gmap) {
            if (is_array($gmap)) {
                $this->assignVars($gmap);
            } else {
                $$gmapHandler = Efqdirectory\Helper::getInstance()->getHandler('Gmap');
                $objGmap      =& $$gmapHandler->get($directory);
                foreach ($objGmap->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                unset($objGmap);
            }
        }
    }

    /**
     * @param string $key
     */
    public function setKey($key = '')
    {
        $this->_key = $key;
    }

    /**
     * Set the value of the script that triggers the points to be added to the google map.
     * @param $pointsJS
     */
    public function setPointsJS($pointsJS)
    {
        $this->_jsPointsArray = $pointsJS;
        $this->_map           = '';
    }

    /**
     * Get the value of the script that triggers the points to be added to the google map.
     */
    public function getPointsJS()
    {
        return $this->_jsPointsArray;
    }

    /**
     * @return mixed
     */
    public function showMap()
    {
        return $this->_map;
    }

    public function generateMap()
    {
        $this->_map .= $this->printPlaceHolder();
        $this->_map .= $this->printScript($this->_jsPointsArray, $this->_key);
        $this->_map .= $this->printTrigger();
    }

    /**
     * @param int $width
     * @param int $height
     * @return string
     */
    public function printPlaceHolder($width = 700, $height = 500)
    {
        return '<div id="map" style="width:' . $width . 'px; height:' . $height . 'px"></div>';
    }

    /**
     * @param string $jsPointsArray
     * @param string $key
     * @return string
     */
    public function printScript($jsPointsArray = '', $key = '')
    {
        global $icmsPreloadHandler;

        $icmsPreloadHandler->addPreloadEvents('gmap.php');
        $gmapScript = <<<EOH
<script type="text/javascript">
var mArray = Array();
var map;
var centerPoint = new GLatLng(40.078071,-101.689453);

function load() {
	doLoad();
	$jsPointsArray;
	addMarkers();
}

function doLoad() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"));
		map.setCenter(centerPoint, 7);
		map.addControl(new GScaleControl());
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		GEvent.addListener(map, 'click', mapClick);

	}
}

function addMarkers() {
	if (mArray.length) {
		var bounds = new GLatLngBounds();
		for (n=0 ; n < mArray.length ; n++ ) {
			var mData = mArray[n].split(';');
			var point = new GLatLng(mData[0],mData[1]);
			bounds.extend(point);
			var marker = createMarker(point, mData[2]);
			map.addOverlay(marker);
		}
		map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds)); 
	}
}

function createMarker(point, title) {
	var marker = new GMarker(point,{title:title});
	GEvent.addListener(marker, "click", function() {
		marker.openInfoWindowHtml('<div style="width:250px;">' + title + '<hr>Lat: ' + point.y + '<br>Lon: ' + point.x + '</div>');
	});
	return marker;
}

function mapClick(marker, point) {
	if (!marker) {
		oLat = document.getElementById("lat");
		oLat.value = point.y;
		oLon = document.getElementById("lon");
		oLon.value = point.x;
		oDesc = document.getElementById("desc");
		oDesc.value = 'New point';


	}
}
</script>\n
EOH;

        return $gmapScript;
    }

    /**
     * Adds a script which triggers the other javascript code to execute
     */
    public function printTrigger()
    {
        $trigger = <<<EOH
<script>		
if (window.onload){
    var oldonload = window.onload;
    window.onload=function(){
        oldonload();
        yourfunctionhere();
    }
}else{
    window.onload=function(){
        yourfunctionhere();
    }
}
</script>\n
EOH;
    }

    /**
     * Function setData sets class from array.
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param $arr
     * @return bool true if insertion is succesful, false if unsuccesful
     * @internal  param object $obj object
     *
     */
    public function setData($arr)
    {
        if (is_array($arr)) {
            $vars = $this->getVars();
            foreach ($vars as $k => $v) {
                $this->setVar($k, $arr[$k]);
            }
        } else {
            return false;
        }

        return true;
    }
}
