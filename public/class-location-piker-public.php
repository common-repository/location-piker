<?php

class Location_Piker_Public {

	private $plugin_name;
	private $version;
    protected static $table ;
    
	public function __construct( $plugin_name, $version ) {
        global $wpdb; 
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        self::$table = $wpdb->prefix .'postmeta';
        add_shortcode( 'location', array($this,'location_piker_shoetcode'));  
	}

	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/location-piker-public.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'location-piker-fancybox-css',plugin_dir_url( __FILE__ ).'inc/jquery.fancybox-1.3.4.css',array(), $this->version, 'all' );
       
	}


	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/location-piker-public.js', array( 'jquery' ), $this->version, false );
			
	    wp_enqueue_script('location-piker-fancybox',plugin_dir_url( __FILE__ ).'inc/jquery.fancybox-1.3.4.js',array( 'jquery' ), $this->version, false );
	  
	}

	public function location_piker_shoetcode($atts){

		if ( ! is_array($atts ) ){return '';}
		$hl = "";
		$geocode = "";
		$id = isset($atts['id']) ? $atts['id'] : '0';
		$location = $this->_queryfor_single_id($id);
		

	    $mapurl = "http://maps.google.com/?output=embed&amp;f=q&amp;";
	    // “hl” stands for “host language”.
		if($hl != ""){
			$mapurl = $mapurl . "hl=".$hl."&amp;";
		}
		
		// “geocode” is a concatination of “geocode” encoded values for waypoints used in directions.
		if($geocode != ""){
			$mapurl = $mapurl . "geocode=".$geocode."&amp;";
		}
	    // “q” stands for “query” and anything passed in this parameter is treated as if it had been typed into the query box on the maps.google.com page.
		if($location['address'] != ""){
			$mapurl = $mapurl . "q=".$location['address']."&amp;";
			
		}
	     // “ll” stands for Latitude,longitude of a Google Map center – Note that the order has to be latitude first, then longitude and it has to be in decimal format.
		if($location['latitude'] != "" && $location['longitude'] != ""){
			$mapurl = $mapurl . "ll=".$location['latitude'].",".$location['longitude']."&amp;";
			
		}
	   // 	“layer” Activates overlay. Current option is “t” traffic.
		if($location['layers'] != ""){
			if ($location['layers'] == "TrafficLayer"){
				$mapurl = $mapurl . "layer=t&amp;";
			}
			else if($location['layers'] == "TransitLayer"){
				$mapurl = $mapurl . "layer=t&amp;";
			}
			else if($location['layers'] == "BicyclingLayer"){
				$mapurl = $mapurl . "layer=t&amp;";
			}
			else if($location['layers'] == "PanoramioLayer"){
				$mapurl = $mapurl . "layer=t&amp;";
			}
			else{
				$mapurl = $mapurl . "layer=t&amp;";
			}
			//echo $mapurl . '<br />';
		  }

		  // heading
			if($location['heading'] != ""){
				$mapurl = $mapurl . "hq=".$location['heading']."&amp;";
			}
			
			// “radius” localizes results to a certain radius. Requires “sll” or similar center point to work.
			//$mapurl = $mapurl . "radius=15000&amp;";
			
			// “t” is Map Type. The available options are “m” map, “k” satellite, “h” hybrid, “p” terrain.
			if($location['maptype'] != ""){
				if ($location['maptype'] == "ROADMAP")
				{
					$mapurl = $mapurl . "t=m&amp;";
				}
				else if($location['maptype'] == "SATELLITE")
				{
					$mapurl = $mapurl . "t=k&amp;";
				}
				else if($location['maptype'] == "HYBRID")
				{
					$mapurl = $mapurl . "t=h&amp;";
				}
				else if($location['maptype'] == "TERRAIN")
				{
					$mapurl = $mapurl . "t=p&amp;";
				}
				else
				{
					$mapurl = $mapurl . "t=m&amp;";
				}
				
			}
			// 	“z” sets the zoom level.
			if($location['zoom'] != ""){
				$mapurl = $mapurl . "z=".$location['zoom']."&amp;";
			}

		    $mapurl;
		    $mapid = "location_piker_popup".$id;
			
			$lpfb = "";
			$lpfb = $lpfb.'<script type="text/javascript"> ';
			$lpfb = $lpfb.' jQuery(document).ready(function() { ';
				$lpfb = $lpfb.' jQuery(".'.$mapid.'").fancybox({ ';
				$lpfb = $lpfb." 'width': '".$location['width']."', ";
				$lpfb = $lpfb." 'height': '".$location['height']."', ";
				$lpfb = $lpfb." 'transitionIn': 'fade', ";
				$lpfb = $lpfb." 'transitionOut': 'fade', ";
				$lpfb = $lpfb." 'autoScale': true, ";
				$lpfb = $lpfb." 'centerOnScroll': true, ";
				$lpfb = $lpfb." 'overlayColor': '#666', ";
				$lpfb = $lpfb." 'titleShow': true ";
				$lpfb = $lpfb." });";
			$lpfb = $lpfb." }); ";
			$lpfb = $lpfb."</script> ";
			$lpfb = $lpfb.'<a href="'. $mapurl.'" title="'.esc_html(stripslashes($location['message'])).'" class="'.$mapid.' iframe">';
			$lpfb = $lpfb.esc_html(stripslashes($location['heading']));
			$lpfb = $lpfb."</a>";
			return $lpfb;
		
	}
    
      protected function _queryfor_single_id($id){

         global $wpdb;                                
         $table =  self::$table; 
         $query  = "SELECT `meta_id`, `meta_value` FROM $table WHERE `meta_id`='".$id."' ";                        
         $result = $wpdb->get_results($query);
         foreach ( $result as $data ){                 
                     $results = unserialize($data->meta_value) ;
         } 
       
         return $results;
        
     }/************ End the Queryfor_Single_Id Function *******/

}
