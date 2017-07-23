<?php

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

/**
 * Class efqGmap
 * Manages operations for google maps
 * 
 * @package efqDirectory
 * @author EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version 1.1.0
 */
class efqGmap extends XoopsObject
{
    private $_lon; //longitude
    private $_lat; //lattitude
    private $_descr; //description
    private $_key; //developer's key
    private $_zoomlevel; //zoom level of google map
    private $_jsPointsArray; //javascript assigning points to google map
    private $_map; // generate output for showing the map on a web page
    public $db;
     
    
    
    
    public function __construct($gmap = false)
    {
        global $xoopsModuleConfig;
        $key = $xoopsModuleConfig['gmapkey'];
        $this->setKey($key);
        $this->setPointsJS('');
        
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('dataid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('lat', XOBJ_DTYPE_TXTBOX, null, true);
        $this->initVar('lon', XOBJ_DTYPE_TXTBOX, null, true);
        $this->initVar('descr', XOBJ_DTYPE_TXTAREA);
        
        if ($gmap != false) {
            if (is_array($gmap)) {
                $this->assignVars($gmap);
            } else {
                $$gmap_handler = xoops_getModuleHandler('gmap', $moddir);
                $objGmap =& $$gmap_handler->get($directory);
                foreach ($objGmap->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                unset($objGmap);
            }
        }
    }
    
    public function setKey($key='')
    {
        $this->_key = $key;
    }
    
    /**
     * Set the value of the script that triggers the points to be added to the google map. 
    */
    public function setPointsJS($pointsJS)
    {
        $this->_jsPointsArray = $pointsJS;
        $this->_map = '';
    }
    
    /**
     * Get the value of the script that triggers the points to be added to the google map. 
    */
    public function getPointsJS()
    {
        return $this->_jsPointsArray;
    }
    
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
    
    public function printPlaceHolder($width=700, $height=500)
    {
        return '<div id="map" style="width:'.$width.'px; height:'.$height.'px"></div>';
    }
    
    
    public function printScript($jsPointsArray='', $key='')
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
	$jsPointsArray
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
     * @author EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version 1.0.0
     * 
     * @param   object   $obj object
     * 
     * @return	bool	true if insertion is succesful, false if unsuccesful
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

/**
 * Class efqGmapHandler
 * Manages database operations for google maps
 * 
 * @package efqDirectory
 * @author EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version 1.1.0
 */
class efqGmapHandler extends XoopsObjectHandler
{
    public $db;
    
    public function __construct()
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
    }
    
    /**
     * create instance of efqGmap class or reset the existing instance.
     * 
     * @return object $efqGmap
     */
    public function &create($isNew = true)
    {
        $gmap = new efqGmap();
        if ($isNew) {
            $gmap->setNew();
        }
        return $gmap;
    }
    
    /**
     * retrieve all points from the database 
     * 
     * @param int $dirid ID of the directory
     * @return mixed reference to the {@link efqGmap} object, FALSE if failed
     */
    public function getPointsJS($gmap)
    {
        if (!is_object($gmap)) {
            return false;
        }
        $sql = 'SELECT * FROM '.$this->db->prefix('efqdiralpha1_gmaps');
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $gmap =& $this->create(false);
        $javaScript = '';
        while ($row = $this->db->fetchArray($result)) {
            //$row{'descr'} = addslashes($row{'descr'});
            $row['descr'] = addslashes($row['descr']);
            //$row{'descr'} = str_replace(';',',',$row{'descr'});
            $row['descr'] = str_replace(';', ',', $row['descr']);
            //$javaScript .= "mArray.push('{$row{'lat'}};{$row{'lon'}};{$row{'descr'}}')\n";
            //echo $row['lat'];
            $javaScript .= "mArray.push('{".$row['lat'] . '};{' . $row['lon'] . '};{' . $row['descr'] . "}')\n";
        }
        $gmap->setPointsJS($javaScript);
        return true;
    }
    
    public function getGmapById($id=0)
    {
        $arr = array();
        $sql = sprintf('SELECT * FROM %s WHERE id=%u',
                       $this->db->prefix('efqdiralpha1_gmaps'), (int)$id);
        $result = $this->db->query($sql) or $eh->show('0013');
        while (list($id, $lat, $lon, $descr, $dataid) = $this->db->fetchRow($result)) {
            $arr = array('id'=>$id, 'lat'=>$lat, 'lon'=>$lon, 'descr'=>$descr, 'dataid'=>$dataid);
        }
        return $arr;
    }
    
    public function getByDataId($id=0)
    {
        if ($id == false) {
            return false;
        }
        $id = (int)$id;
        echo $id;
        if ($id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('efqdiralpha1_gmaps') . ' WHERE dataid=' . (int)$id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $gmap =& $this->create(false);
            $gmap->assignVars($this->db->fetchArray($result));
            return $gmap;
        }
        return false;
    }

    /**
     * Function insertGmap inserts google map data into DB
     * @author EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version 1.0.0
     * 
     * @param   object   $obj object
     * 
     * @return	bool	true if insertion is succesful, false if unsuccesful
     */
    public function insertGmap($obj, $forceQuery=false)
    {
        $tablename = 'efqdiralpha1_gmaps';
        $keyName = 'id';
        if ($obj instanceof efqGmap) {
            // Variable part of this function ends. From this line you can copy
            // this function for similar object handling functions.
            $obj->cleanVars();
            $cleanvars = $obj->cleanVars;
        } else {
            return false;
        }
        $countVars = count($cleanvars);
        $i = 1;
        $strFields = '';
        $strValues = '';
        foreach ($cleanvars as $k => $v) {
            $strFields .= $k;
            $strValues .= "'".$v."'";
            if ($i < $countVars) {
                $strFields .= ', ';
                $strValues .= ', ';
            }
            $i++;
        }
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->db->prefix($tablename), $strFields, $strValues);
        if ($forceQuery) {
            if ($this->db->queryF($sql)) {
                $itemid = $this->db->getInsertId();
                $obj->setVar($keyName, $itemid);
                return true;
            }
        } else {
            if ($this->db->query($sql)) {
                $itemid = $this->db->getInsertId();
                $obj->setVar($keyName, $itemid);
                return true;
            }
        }
        return false;
    }

    
    /**
     * Function updateGmap updates google map data
     * @author EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version 1.0.0
     * 
     * @param   object   $obj object
     * 
     * @return	bool	true if update is succesful, false if unsuccesful
     */
    public function updateGmap($obj, $forceQuery=false)
    {
        $tablename = 'efqdiralpha1_gmaps';
        $keyName = 'id';
        if ($obj instanceof efqGmap) {
            // Variable part of this function ends. From this line you can copy
            // this function for similar object handling functions.
            $obj->cleanVars();
            $cleanvars = $obj->cleanVars;
            $keyValue = $obj->getVar($keyName);
        } else {
            return false;
        }
        $countVars = count($cleanvars);
        $i = 1;
        $strSet = '';
        $strValues = '';
        foreach ($cleanvars as $k => $v) {
            if ($k != 'id') {
                $strSet .= $k . '=' . "'" . $v . "'";
                if ($i < $countVars) {
                    $strSet .= ', ';
                }
            }
            $i++;
        }
        $sql = sprintf('UPDATE %s SET %s WHERE %s = %u', $this->db->prefix($tablename), $strSet, $keyName, $keyValue);
        if ($forceQuery) {
            if ($this->db->queryF($sql)) {
                return true;
            }
        } else {
            if ($this->db->query($sql)) {
                return true;
            }
        }
        return false;
    }
}
