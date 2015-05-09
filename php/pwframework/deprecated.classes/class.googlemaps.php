<?php

/** 
 * @package		Rokin Gallery
 * @author    	Pageworks http://www.pageworks.nl
 * @copyright	Copyright (c) 2006 - 2010 Pageworks. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
if(!(defined('_VALID_MOS') || defined( '_JEXEC' ))){
	die( 'Restricted access' );
}

/**
 * renders a google map object
 *
 */
class GoogleMap{
	
	var $centreLat;
	var $centreLng;
	var $markers = array();
	var $key;
	var $mapId;
	//0=map 1=satelite 2=Hybride
	var $view_mode = 0;
	var $width=400;
	var $height=300;
	var $zoomlevel=12;
	var $encoded_polyline;
	var $control_overview='on';	
	var $control_dragging='on';
	var $control_scrollwheel='on';
	var $control_large_map='on';
					
	function version(){
		return "v 1.0.2";
		/**
		 * change the rendering the markers
		 */
	}
	/**
	 * Renders a google map 
	 *
	 * @param unknown_type $centre Latitude
	 * @param unknown_type $centre longitude
	 * @param unknown_type $key googla maps key needed to enable.
	 * @param unknown_type $mapId, the id of a div element to render the map on. If left empty a default element is created rendering the map
	 * @return GoogleMap
	 */
	function GoogleMap($centreLat, $centreLng, $key, $mapId=null){
		$this->centreLat = $centreLat;
		$this->centreLng = $centreLng;
		$this->key = $key;
		$this->mapId = $mapId;
	}
	
	function addMarker($marker){
		$this->markers[] = $marker;
	}
	
	function render(){?>
	<!-- development key
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAABvfdgPZSF79bVvpHb4hwiRT2yXp_ZAY8_ufC3CFXhHIE1NvwkxT-5byCSu2k4kUEihtYeSUTFHRIhg" type="text/javascript"></script>	
	-->
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php  echo $this->key;?>" type="text/javascript"></script>
	<script type="text/javascript" src="<?php  echo get_pwframework_site() ?>/js/mapsTooltip.js"></script>
	<style>
		.tooltip{
			color:#000;	
			background-color:#eeeeee;
			font-family:tahoma;
			font-size:8pt;
			padding:2px 4px 2px 4px;
			border:solid 1px #cccccc;
			white-space:nowrap;
		}
		
	</style>
    	<script type="text/javascript">
    	
    		
    	 	var iconRed = new GIcon(); 
		    iconRed.image = 'http://labs.google.com/ridefinder/images/mm_20_red.png';
		    iconRed.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
		    iconRed.iconSize = new GSize(12, 20);
		    iconRed.shadowSize = new GSize(22, 20);
		    //iconRed.image = '<?php  echo $mosConfig_live_site . "/components/com_campatrol/img/camera.png";?>';
		    //iconRed.shadow = '<?php  echo $mosConfig_live_site . "/components/com_campatrol/img/camera.shadow.png";?>';
			//iconRed.iconSize = new GSize(24, 24);
			//iconRed.shadowSize = new GSize(24, 24);
		    iconRed.iconAnchor = new GPoint(6, 20);
		    iconRed.infoWindowAnchor = new GPoint(5, 1);
		    
		  	/* default marker maker */
		    function createMarker(point, html) {
     			 var marker = new GMarker(point, iconRed);     			 
      			 GEvent.addListener(marker, 'click', function() {
        			 marker.openInfoWindowHtml(html);
      				});
		      return marker;
		    }
		    
		   
		    
		    function load_maps(){        	    	    
		    
		    	if (GBrowserIsCompatible()) {
	        		var map = new GMap2(document.getElementById('<?php  if($this->mapId==null){ echo "map"; }else{ echo  $this->mapId;}  ?>'));
	        		
	        		map.setCenter(new GLatLng(parseFloat("<?php  echo $this->centreLat; ?>"), parseFloat("<?php  echo $this->centreLng; ?>")), <?php  echo $this->zoomlevel; ?>);
					map.addControl(new GMapTypeControl());
					<?php  
					if($this->control_dragging=='on'){
						echo "map.enableDragging();";	
					}else{
	        			echo "map.disableDragging();";
	        		}
					
					if($this->control_scrollwheel=='on'){
						echo "  map.enableScrollWheelZoom();";	
					}
					
					if($this->control_large_map=='on'){
						echo "map.addControl(new GLargeMapControl ());";	
					}else{
						echo "map.addControl(new GSmallMapControl ());";
					}
					
					if($this->control_overview=='on'){
						echo "map.addControl(new GOverviewMapControl 	());";
					}
					
					
	        		if($this->locate_mode){
	        			?>
	        			
						GEvent.addListener(map,"click", function(overlay,point) {
					    if (point) {
					      GAsync(map, 'fromLatLngToDivPixel', [point], 'getZoom', function(divPixel, zoom) {
					        var myHtml = "The GPoint value is: " + divPixel + " at zoom level " + zoom;
					        map.openInfoWindow(point, myHtml);
					      });
					    }
					  });
				  <?php 
	        		}
	        		
	        		//the view mode
	        		if ($this->view_mode==1) {
	        		echo "map.setMapType(G_SATELLITE_MAP); \n";
	        		}else if($this->view_mode==2){
	        			echo "map.setMapType(G_HYBRID_MAP); \n";
	        		}else{
	        			echo "map.setMapType(G_NORMAL_MAP); \n";
	        		}
	        		
	        		
	        		if(isset($this->encoded_polyline) && strlen($this->encoded_polyline) > 2){
	        			?>
	        			var encodedPolyline = new GPolyline.fromEncoded({
						    color: "#007FFF",
						    weight: 4,
						    opacity: 0.8,
						    points: "<?php  echo $this->encoded_polyline;?>",
						    levels: "BBB",
						    zoomFactor: 32,
						    numLevels: 4
						});
						map.addOverlay(encodedPolyline);
					<?php 
	        		}
	        		
	        	
	        		
	        		//****************#####################*********************
	        		//create the markers
	        		foreach($this->markers as $marker){	        		
	        			$marker->render();
	        		}?>
		    	}
		    }
		    jQuery(document).ready(function(){
		    	load_maps();	
		    			    		
		   	})
    	</script>
    	<?php 
		if($this->mapId==null){
    	?> <div id="map" style="overflow: hidden; width: <?php  echo $this->width;?>px; height: <?php  echo $this->height;?>px">&nbsp;</div> <?php 
		}
	}
}


class GoogleMarker{
	var $tooltip;
	var $lat;
	var $lng;
	var $baloonText;
	var $onClick;
	
	function version(){
		return "v 1.0.1";
		/** 29-06-2008
		 * change the rendering the markers, added render method so it can be overrided when extendend
		 */
	}
	/**
	 * Constructor of the GoogleMarker
	 *
	 * @param unknown_type $lat
	 * @param unknown_type $lng
	 * @param unknown_type $baloonText
	 * @param unknown_type $tooltip
	 * @param unknown_type $markerMakerFunction javascript function name that will be called the create the marker
	 * @param unknown_type $iconImageUrl
	 * @param unknown_type $iconShadowUrl
	 * @return GoogleMarker
	 */
	function GoogleMarker($lat, $lng, $baloonText, $tooltip=null){
		$this->lat = $lat;
		$this->lng = $lng;
		$this->baloonText = $baloonText;		
		$this->tooltip = $tooltip;	
	}
	
	function render(){
		echo "var point = new GLatLng(".  $this->lat .",". $this->lng ."); \n";
        if ($this->icon == null){
	  		echo "var marker = new GMarker(point, iconRed); \n";
        }else{
        	echo "var marker = new GMarker(point, $item->icon); \n";
        }
		
		if(isset($this->onClick)){
			echo "GEvent.addListener(marker, 'click', function() {
					 " .  $this->onClick . "        						 
					 }); \n";							
		}
		if(isset($this->baloonText)){
			echo "GEvent.addListener(marker, 'click', function() {
					 marker.openInfoWindowHtml(\"<p style='color:black;'>" .  $this->baloonText . "</p>\");        						 
					 }); \n";
		}
		if(isset($this->tooltip)){
			?>
			var tooltip = new Tooltip(marker,'<?php  echo $this->tooltip?>',4); 
			marker.tooltip = tooltip;
			map.addOverlay(tooltip); 
			GEvent.addListener(marker,'mouseover',function(){ this.tooltip.show(); }); 
			GEvent.addListener(marker,'mouseout',function(){ this.tooltip.hide(); });
			<?php 
		}
		
		//adding it  to the map
		echo "map.addOverlay(marker);";
	}
	
	
}


/* example */
/*
	$map = new GoogleMap($region->lat, $region->lng, $settings->google_maps_key);
	$map->view_mode=0; // 0 = map - 1 = satelite - 2 = hybrid
	$map->width=540;
	$map->height=460;
	$map->zoomlevel=8;
	if(isset($region->encoded_polyline)){
		$map->encoded_polyline = $region->encoded_polyline;
	}
	foreach ($cities as $city){
		$marker = new GoogleMarker($city->lat, $city->lng,null,$city->city);
		$marker->onClick="window.location='index.php?option=com_campatrol&Itemid=$Itemid&unit=camera&act=city_view&id=$city->id';";		
		
		$map->addMarker($marker);
	}
	
	$map->render();
*/
