<?php

class Location_Piker_Admin {


	private $plugin_name;
	private $version;
    protected static $table ;

	public function __construct( $plugin_name, $version ) {
    global $wpdb; 
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		self::$table = $wpdb->prefix .'postmeta';
		
		add_action( 'admin_menu',array($this,'location_piker_page_creater')  ) ; 
		add_action( 'admin_menu',array($this,'location_piker_setting_api')  ) ;

	}

	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/location-piker-admin.css', array(), $this->version, 'all' );
	    wp_register_style( 'location-piker-bootstrap', plugin_dir_url( __FILE__ ) . 'css/location-piker-bootstrap.css', array(), $this->version , 'all' );
        wp_enqueue_style('location-piker-bootstrap');
        
      

	}

	public function enqueue_scripts() {

		
		wp_enqueue_script( 'location-piker-bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/lokation-piker-bootstrap.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'location-picker-js', plugin_dir_url( __FILE__ ) . 'js/location-picker.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/location-piker-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function location_piker_define_page(){
		
		$parents = array(
			            array(

			                 'page_title'  => 'Location Piker',              //$parent_slug
						     'menu_title'  => 'Location Piker',          //$page_title
						     'capability'  => 'manage_options',           //$capability
						     'menu_slug'   => 'location',              //$menu_title
						     'dashicons'   => 'dashicons-location'    //$dashicons
			            ));

		 return $parents ;

	}

	public function location_piker_define_subpage(){

		$parents = array(  

		               array(

			                 'parent_slug' => 'location',    //$parent_slug
						     'page_title'  => 'Gallery',       //$page_title
						     'menu_title'  => 'Gallery',       //$menu_title
						     'capability'  => 'manage_options', //$capability
						     'menu_slug'   => 'location_piker_gallery', 
			            ) ,                     
			            array(

			                 'parent_slug' => 'location',    //$parent_slug
						     'page_title'  => 'Settings',       //$page_title
						     'menu_title'  => 'Settings',       //$menu_title
						     'capability'  => 'manage_options', //$capability
						     'menu_slug'   => 'location_piker_setting', 
			            )		         
			         
                  );

		return $parents ;
	}

	public function location_piker_create_menu_page(){
        $parents = $this->location_piker_define_page();
        if ( $parents ) {
            foreach ($parents as $parent) {
                add_menu_page(   $parent['page_title'], 
                	             $parent['menu_title'],
                	             $parent['capability'],
                	             $parent['menu_slug'],
                	             array( $this , $parent['menu_slug'].'_callback'),
                	             $parent['dashicons'] ) ; 
             }
        
        }
        
    }

    public function location_piker_create_submenu_page(){
        $parents = $this->location_piker_define_subpage();
        if ( $parents ) {
            foreach ($parents as $parent) {
                add_submenu_page($parent['parent_slug'] , 
                	             $parent['page_title'],
                	             $parent['menu_title'],
                	             $parent['capability'],
                	             $parent['menu_slug'],
                	             array( $this , $parent['menu_slug'].'_callback' )) ; 
             }
        
        }
      }

    public function location_piker_page_creater(){
       	   $this->location_piker_create_menu_page();
       	   $this->location_piker_create_submenu_page();
     }

	public function location_piker_menu(){
		   add_menu_page('location_piker','Location Piker','manage_options','location',array($this,'location_piker'));              
	}

    public function location_piker_insert_sql($unserializedata){
	        global $wpdb; 
	        $serializedata = serialize($unserializedata) ; 
	        $table = self::$table ;	       
	        $wpdb->insert(  $table, array( 'meta_key' => 'location','meta_value' => $serializedata, ) );	    
	        return true;  
     }

     protected function location_piker_update_sql($data,$editID){
           global $wpdb; 
           $serializedata = serialize($data) ; 
           $table = self::$table ;
           $wpdb->replace($table, array( 'meta_key' => 'location','meta_value' => $serializedata,'meta_id' => $editID ) );
           return true;
        
          }


    public function location_piker_delete_id($id='0'){
         global $wpdb;                                
         $table =  self::$table;    
         $wpdb->delete($table , array('meta_id' => $id) );
         return true ;
                          
     }
    public function location_piker_select_all_sql(){
              global $wpdb;       
              $Path = $_SERVER['QUERY_STRING'];                   
              $table =  self::$table;

             echo '<div class="location-piker row">';
             echo '<table class="table table-bordered">';
             echo '<thead>';
             echo '<tr style="background:#111;color:#fff;">';
             echo '<th>Map ID</th>';
             echo '<th>Heading</th>';            
             echo '<th>Shortcode</th>';
             echo '<th>Latitude</th>';
             echo '<th>Longitude</th>';
             echo '<th>Width</th>';
             echo '<th>Height</th>';
             echo '<th>Map Type</th>';
             echo '<th>Action</th>';    
             echo '</tr>';
             echo '</thead>';
             echo '<tbody>';
            
             $query  = "SELECT `meta_id`, `meta_value` FROM  $table  WHERE `meta_key`='location' ";  
             $result = $wpdb->get_results($query);
             $i = 1 ;
             foreach ( $result as $data ){

                     $vales = unserialize($data->meta_value) ;                   
                     if(($i%2) == 1){$bg='';}else{$bg='#eee';}                                  
                     echo "<tr style='background:".$bg."'>";                     
                     echo "<td>".$data->meta_id."</td>";
                     echo "<td>". ucwords($vales["heading"])."</td>";
                     echo "<td>[location id=".$data->meta_id." ]</td>";
                     echo "<td>". ucwords($vales["latitude"])."</td>";
                     echo "<td>". ucwords($vales["longitude"])."</td>";
                     echo "<td>". ucwords($vales["width"])."</td>";
                     echo "<td>". ucwords($vales["height"])."</td>";
                     echo "<td>". ucwords($vales["maptype"])."</td>";
                     echo "<td><button type='button' class='button btn-warning'><a  href='?page=location&e=".$data->meta_id."'> Edit</a></button>"
                           .' '."<button type='button' class='button btn-danger'><a  href='?page=location&d=".$data->meta_id."'>Delete</a></button></td>";
                     
                     echo "</tr>";
                     $i++;
                }
             echo '</tbody>';
             echo '</table>'; 
             echo '</form>';     
             echo '</div>';             
        
     }     

    public function queryfor_single_id($id){
         global $wpdb;                                
         $table =  self::$table; 
         $query  = "SELECT `meta_id`, `meta_value` FROM $table WHERE `meta_id`='".$id."' ";                        
         $result = $wpdb->get_results($query);
         foreach ( $result as $data ){                 
                     $results = unserialize($data->meta_value) ;
         } 
         //var_dump($results);
         return $results;
        
    }
    public function refrace(){?> 
             <script>location.reload();</script><?php 
    }
    public function redirect(){?>
             <script> window.location="?page=location&s=0001";</script><?php
    }
	public function location_callback(){ 

		if( get_option( 'google_map_api_key' ) == false ):

			die('<h1>Please setting the Location Piker properly and set your google api key</h1>');

		endif ;

		  $location = array(
					'id'                  => '',
					'heading'             => '',
					'address'             => '',
					'latitude'            => '',
					'longitude'           => '',
					'message'             => '',
					'draggable'           => '',
					'width'               => '',
					'height'              => '',
					'zoom'                => '',
					'maptype'             => '',
					'turnoffscrolling'    => '',
					'enablevisualrefresh' => '',
					'imagery'             => '',
					'layers'              => '',
					'turnoffpan'          => '',
					'turnoffzoom'         => '',
					'turnoffmaptype'      => '',
					'turnoffscale'        => '',
					'turnoffstreetview'   => '',
					'turnoffoverviewmap'  => '',
					'streetview'          => ''
				);
      
         // Form submitted, check the data

	    @ $location = $_POST['location'];
        if (isset($location['lp_submit']) && !empty($location['wp_create_nonce'])){
	        //	Just security thingy that wordpress offers us
	            check_admin_referer('lp_form_add');
	
	            $location['id'] = isset( $location['id'] ) ? sanitize_text_field( $location['id'] ) : '';	            
	            $location['heading'] = isset( $location['heading'] ) ? sanitize_text_field( $location['heading'] ) : '';	            
	            $location['address'] = isset( $location['address'] ) ? sanitize_text_field( $location['address'] ) : '';
	            $location['latitude'] = isset( $location['latitude'] ) ? sanitize_text_field( $location['latitude'] ) : '';
	            $location['longitude'] = isset( $location['longitude'] ) ? sanitize_text_field( $location['longitude'] ) : '';
	            $location['message']   = isset( $location['message'] ) ? sanitize_text_field( $location['message'] ) : '';
	            $location['draggable'] = isset( $location['draggable'] ) ? sanitize_text_field( $location['draggable'] ) : '';
	            $location['width']     = isset( $location['width'] ) ? sanitize_text_field( $location['width'] ) : '';
	            $location['height'] = isset( $location['height'] ) ? sanitize_text_field( $location['height'] ) : '';
	            $location['zoom'] = isset( $location['zoom'] ) ? sanitize_text_field( $location['zoom'] ) : '';
	            $location['maptype'] = isset( $location['maptype'] ) ? sanitize_text_field( $location['maptype'] ) : '';
	            $location['turnoffscrolling'] = isset( $location['turnoffscrolling'] ) ? sanitize_text_field( $location['turnoffscrolling'] ) : '';
	            $location['enablevisualrefresh'] = isset( $location['enablevisualrefresh'] ) ? sanitize_text_field( $location['enablevisualrefresh'] ) : '';
	            $location['imagery'] = isset( $location['imagery'] ) ? sanitize_text_field( $location['imagery'] ) : '';
	            $location['layers'] = isset( $location['layers'] ) ? sanitize_text_field( $location['layers'] ) : '';
	            $location['turnoffpan'] = isset( $location['turnoffpan'] ) ? sanitize_text_field( $location['turnoffpan'] ) : '';
	            $location['turnoffzoom'] = isset( $location['turnoffzoom'] ) ? sanitize_text_field( $location['turnoffzoom'] ) : '';
	            $location['turnoffmaptype'] = isset( $location['turnoffmaptype'] ) ? sanitize_text_field( $location['turnoffmaptype'] ) : '';
	            $location['turnoffscale'] = isset( $location['turnoffscale'] ) ? sanitize_text_field( $location['turnoffscale'] ) : '';
	            $location['turnoffstreetview'] = isset( $location['turnoffstreetview'] ) ? sanitize_text_field( $location['turnoffstreetview'] ) : '';
	            $location['turnoffoverviewmap'] = isset( $location['turnoffoverviewmap'] ) ? sanitize_text_field( $location['turnoffoverviewmap'] ) : '';
	            $location['streetview'] = isset( $location['streetview'] ) ? sanitize_text_field( $location['streetview'] ) : '';
	            
	            if( ! $location['id'] ==''){

	                $location_piker_update_sql = $this->location_piker_update_sql($location,$location['id']);
	                if( $location_piker_update_sql ):?>
	            	<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
	                  <p><strong> Successfully updated  .</strong></p>
	                  <button type="button" class="notice-dismiss">
	                  <span class="screen-reader-text">Dismiss this notice.</span></button>
	                </div>   
                    <?php 
	            	endif;

	            }else{
	            	$location_piker_insert_sql = $this->location_piker_insert_sql($location);
	            	if( $location_piker_insert_sql ):?>
	            	<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
	                  <p><strong> Successfully created new location .</strong></p>
	                  <button type="button" class="notice-dismiss">
	                  <span class="screen-reader-text">Dismiss this notice.</span></button>
	                </div>   
                    <?php 
	            	endif;
	            }
          }else{
          	    $location = array(
        					'id'                  => '',
        					'heading'             => '',
        					'address'             => '',
        					'latitude'            => '',
        					'longitude'           => '',
        					'message'             => '',
        					'draggable'           => '',
        					'width'               => '',
        					'height'              => '',
        					'zoom'                => '',
        					'maptype'             => '',
        					'turnoffscrolling'    => '',
        					'enablevisualrefresh' => '',
        					'imagery'             => '',
        					'layers'              => '',
        					'turnoffpan'          => '',
        					'turnoffzoom'         => '',
        					'turnoffmaptype'      => '',
        					'turnoffscale'        => '',
        					'turnoffstreetview'   => '',
        					'turnoffoverviewmap'  => '',
        					'streetview'          => ''
        				);
          }
		?>
         
	 <?php 
	 if(isset($_GET['e'])){$id = intval( $_GET['e'] ) ; $location = $this->queryfor_single_id($id);}   
     if(isset($_GET['d'])){$id = intval( $_GET['d'] ) ; $this->location_piker_delete_id($id); $this->redirect();}       
     if(isset($_GET['s'])&&($_GET['s']==='0001')){?>
     <span id="shortcodedispaly" value="<?php echo $_GET['s'] ; ?>"></span> <?php
     }
	   ?> 
	   <script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places&key=<?php echo get_option( "google_map_api_key" )?>'></script>          
	   <script type="text/javascript">
     (function($) {
              "use strict";        
				               jQuery(document).ready(function(){
					                var latitude  = "<?php if(isset($location['latitude'])){echo $location['latitude'] ;}else{ echo '23.810332';} ?>"; 
					                var longitude = "<?php if(isset($location['longitude'])){echo $location['longitude'] ;}else{ echo '90.41251809999994';} ?>"; 
					                var address   = "<?php if(isset($location['address'])){echo $location['address'];}else{ echo 'Road No 11, Dhaka, Bangladesh';} ?>";  
					                var radius    = "<?php if(isset($location['radius'])){echo $location['radius'];}else{ echo '30';} ?>";  
					               // alert(latitude);           					               
					                jQuery('#location_piker').locationpicker({
						                  location: {latitude: latitude, longitude: longitude},
						                  radius: radius,
						                  inputBinding: {
						                      latitudeInput:  jQuery('#lp_latitude'),
						                      longitudeInput: jQuery('#lp_longitude'),
						                      radiusInput: jQuery('#lp_radius'),
						                      locationNameInput: jQuery('#lp_address')
						                   },
							                 enableAutocomplete: true,
							                 onchanged: function (currentLocation, radius, isMarkerDropped) {
							                  // Uncomment line below to show alert on each Location Changed event
							                  //alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
							                  }
						              });
				                });

					      })(jQuery); /* end the ready function */ 
			  </script>


			  <p class="location-piker row" >
			  <span class="col-sm-12">	         
	          <span class="heading1"><?php _e('Welcome to Location Piker', 'location-piker'); ?></span>
              </span>
              <p><!-- End heading -->    

                
              
	          <form name="lp_form" method="post" action="" id="location_add_form"  >
	          	 <div class="location-piker row" id="location-piker-form">
                 
	          	 	<span class="col-sm-4">
                       
		                
		                <input  name="location[id]" type="hidden" value="<?php echo $_GET['e'] ;  ?>"  />   
			  	        <label for="tag"><?php _e('Location Heading', 'location piker'); ?></label>
				        <input class="form-control" name="location[heading]" type="text" id="lp_heading" value="<?php  if(isset($location['heading'])){echo $location['heading'] ;}  ?>" required />
				        <p><?php _e('Enter heading for your google map.', 'location piker'); ?></p>
				
				        <label for="tag"><?php _e('Map Address', 'location piker'); ?></label>
				        <input class="form-control" type="text" name="location[address]" id="lp_address" size="60" value="<?php  if(isset($location['address'])){echo $location['address'] ;} ?>"  required  />
				        <p><?php _e('Enter the map address. Google auto suggest helps you to choose one.', 'location piker'); ?></p>
			         	          	 	 
						<input  class="form-control" type="hidden" name="location[latitude]" id="lp_latitude" placeholder="<?php _e('Latitude', 'location piker')?>"  value="<?php  if(isset($location['latitude'])){echo $location['latitude'] ;}else{ echo '23.810332' ;} ?>" />						            															
						<!-- <p><?php _e('Latitude auto change abale by map address.', 'location piker'); ?></p>	 -->

						<input  class="form-control" type="hidden" name="location[longitude]" id="lp_longitude" placeholder="<?php _e('Longitude', 'location piker')?>"   value="<?php  if(isset($location['longitude'])){echo $location['longitude'] ;}else{ echo '90.41251809999994'; }  ?>" />
						<!-- <p><?php _e('Longitude auto  change abale by map address.', 'location piker'); ?></p>	 -->	            																		
						
						<label for="tag"><?php _e('Short Message', 'location piker'); ?></label>
						<input class="form-control" name="location[message]" type="text" id="lp_message"   value="<?php  if(isset($location['message'])){echo $location['message'] ;}  ?>"  maxlength="255" />
						<p><?php _e('Enter short message for this address.', 'location piker'); ?></p>
                         
                       
						<label for="tag-a"><?php _e('Width', 'location piker'); ?></label>
						<select class="form-control" name="location[width]" id="lp_width">
							<option value='30%' <?php if(isset($location['width']) && ($location['width']=='30%') ){echo 'selected="selected"' ;} ?> selected="selected">30%</option>
							<option value='35%' <?php if(isset($location['width']) && ($location['width']=='35%') ){echo 'selected="selected"' ;} ?> >35%</option>
							<option value='40%' <?php if(isset($location['width']) && ($location['width']=='40%') ){echo 'selected="selected"' ;} ?> >40%</option>
							<option value='45%' <?php if(isset($location['width']) && ($location['width']=='45%') ){echo 'selected="selected"' ;} ?> >45%</option>
							<option value='50%' <?php if(isset($location['width']) && ($location['width']=='50%') ){echo 'selected="selected"' ;} ?> >50%</option>
							<option value='55%' <?php if(isset($location['width']) && ($location['width']=='55%') ){echo 'selected="selected"' ;} ?> >55%</option>
							<option value='60%' <?php if(isset($location['width']) && ($location['width']=='60%') ){echo 'selected="selected"' ;} ?> >60%</option>
							<option value='65%' <?php if(isset($location['width']) && ($location['width']=='65%') ){echo 'selected="selected"' ;} ?> >65%</option>
							<option value='70%' <?php if(isset($location['width']) && ($location['width']=='70%') ){echo 'selected="selected"' ;} ?> >70%</option>
							<option value='75%' <?php if(isset($location['width']) && ($location['width']=='75%') ){echo 'selected="selected"' ;} ?> >75%</option>
							<option value='80%' <?php if(isset($location['width']) && ($location['width']=='80%') ){echo 'selected="selected"' ;} ?> >80%</option>
							<option value='85%' <?php if(isset($location['width']) && ($location['width']=='85%') ){echo 'selected="selected"' ;} ?> >85%</option>
							<option value='90%' <?php if(isset($location['width']) && ($location['width']=='90%') ){echo 'selected="selected"' ;} ?> >90%</option>
						</select>
						<p><?php _e('Select your width percentage for the map.', 'google-map-with-fancybox-popup'); ?></p>
						

						
						<label for="tag-a"><?php _e('Height', 'location piker'); ?></label>
						<select class="form-control" name="location[height]" id="lp_height">
							<option value='30%' <?php if(isset($location['height']) && ($location['height']=='30%') ){echo 'selected="selected"' ;} ?> selected="selected">30%</option>
							<option value='35%' <?php if(isset($location['height']) && ($location['height']=='35%') ){echo 'selected="selected"' ;} ?> >35%</option>
							<option value='40%' <?php if(isset($location['height']) && ($location['height']=='40%') ){echo 'selected="selected"' ;} ?> >40%</option>
							<option value='45%' <?php if(isset($location['height']) && ($location['height']=='45%') ){echo 'selected="selected"' ;} ?> >45%</option>
							<option value='50%' <?php if(isset($location['height']) && ($location['height']=='50%') ){echo 'selected="selected"' ;} ?> >50%</option>
							<option value='55%' <?php if(isset($location['height']) && ($location['height']=='55%') ){echo 'selected="selected"' ;} ?> >55%</option>
							<option value='60%' <?php if(isset($location['height']) && ($location['height']=='60%') ){echo 'selected="selected"' ;} ?> >60%</option>
							<option value='65%' <?php if(isset($location['height']) && ($location['height']=='65%') ){echo 'selected="selected"' ;} ?> >65%</option>
							<option value='70%' <?php if(isset($location['height']) && ($location['height']=='70%') ){echo 'selected="selected"' ;} ?> >70%</option>
							<option value='75%' <?php if(isset($location['height']) && ($location['height']=='75%') ){echo 'selected="selected"' ;} ?> >75%</option>
							<option value='80%' <?php if(isset($location['height']) && ($location['height']=='80%') ){echo 'selected="selected"' ;} ?> >80%</option>
							<option value='85%' <?php if(isset($location['height']) && ($location['height']=='85%') ){echo 'selected="selected"' ;} ?> >85%</option>
							<option value='90%' <?php if(isset($location['height']) && ($location['height']=='90%') ){echo 'selected="selected"' ;} ?> >90%</option>
						</select>
						<p><?php _e('Select your height percentage for the map.', 'location-piker'); ?></p>
                       

                    
						<label for="tag-a"><?php _e('Map Zoom Level', 'location-piker'); ?></label>
						<select class="form-control" name="location[zoom]" id="lp_zoom">
							<?php
							if( !isset($location['zoom']) ){
								$location['zoom']= '';
							}
							$thisselected = "";
							for($i=18; $i > 1; $i--)
							{
								if($i == $location['zoom'] )  
								{ 
									$thisselected = "selected='selected'" ; 
								}
								?>
								<option value='<?php echo $i; ?>' <?php echo $thisselected; ?>><?php echo $i; ?></option>
								<?php
								$thisselected = "";
							}
							?>
						</select>
						<p><?php _e('Select your zoom level for the map.', 'location-piker'); ?></p>
						
						<label for="tag-a"><?php _e('Map Type', 'location piker'); ?></label>
						<select  class="form-control" name="location[maptype]" id="lp_maptype">
							<option value='ROADMAP'     <?php if(isset($location['maptype']) && ($location['maptype']=='ROADMAP') ){echo 'selected="selected"' ;} ?> > ROADMAP</option>
							<option value='SATELLITE'   <?php if(isset($location['maptype']) && ($location['maptype']=='SATELLITE') ){echo 'selected="selected"' ;} ?> > SATELLITE</option>
							<option value='HYBRID'      <?php if(isset($location['maptype']) && ($location['maptype']=='HYBRID') ){echo 'selected="selected"' ;} ?> > HYBRID </option>
							<option value='TERRAIN'     <?php if(isset($location['maptype']) && ($location['maptype']=='TERRAIN') ){echo 'selected="selected"' ;} ?> > TERRAIN </option>
						</select>
						<p><?php _e('Select your map type for the map.', 'location-piker'); ?></p>
						

						
						<label for="tag-a"><?php _e('Select Layers', 'location piker'); ?></label>
						<select class="form-control" name="location[layers]" id="lp_layers">
							<option value='TrafficLayer'  <?php if(isset($location['layers']) && ($location['layers']=='TrafficLayer') ){echo 'selected="selected"' ;} ?> >Traffic Layers</option>
							<option value='TransitLayer'  <?php if(isset($location['layers']) && ($location['layers']=='TransitLayer') ){echo 'selected="selected"' ;} ?> >Transit Layers</option>
							<option value='BicyclingLayer'  <?php if(isset($location['layers']) && ($location['layers']=='BicyclingLayer') ){echo 'selected="selected"' ;} ?> >Bicycling Layers</option>
							<option value='PanoramioLayer'  <?php if(isset($location['layers']) && ($location['layers']=='PanoramioLayer') ){echo 'selected="selected"' ;} ?> >Panoramio Layers</option>
						</select>
						<p><?php _e('Select layer for the map.', 'location-piker'); ?></p>
		               				     
					   
                    </span><!-- end .class="col-sm-5" -->

                     <span class="col-sm-7 input-group">
                     	<br /><br />
                        <label for="tag-a"><?php _e('Map Location', 'location-piker'); ?></label>
                        <p class="form-control" id="location_piker" style="width:100%; height:400px"></p>	 		     
                    </span><!-- end .class="col-sm-5" -->

                    <span class="col-sm-12 input-group">
                         <p class="submit">
                         	<?php 
                         	if(isset($_GET['e'])) : ?>
                             <span class="col-sm-12">
                             	<input  class="button button-primary"  name="location[lp_submit]"  value="<?php _e('Update', 'location-piker'); ?>" type="submit" />
                                <input type="hidden" name="location[wp_create_nonce]" id="wp_create_nonce" value="<?php echo wp_create_nonce( 'gmwfb-add-nonce' ); ?>"/>
                                <button type="reset"  class="button"><?php _e('Cancel', 'location-piker'); ?></button>
                             </span>
                         	<?php else: ?>
                             <span class="col-sm-12">
                             	<input  class="button button-primary"  name="location[lp_submit]"  value="<?php _e('Submit', 'location-piker'); ?>" type="submit" />
                             	<input type="hidden" name="location[wp_create_nonce]" id="wp_create_nonce" value="<?php echo wp_create_nonce( 'gmwfb-add-nonce' ); ?>"/>
                             	<button type="reset"  class="button"><?php _e('Cancel', 'location-piker'); ?></button>
                             </span>
                         	<?php endif; ?>
					       
					      
					        
					     </p>
						  <?php wp_nonce_field('lp_form_add'); ?>
						  					
                    </span><!-- end .class="col-sm-12" -->
                 </div><!-- end #location-piker-form -->
             </form>

              		
           
		  <?php
	}

	public function location_piker_gallery_callback(){
		?>
             <div class="location-piker row">
	          	 	<span class="col-sm-12">
                        <?php $this->location_piker_select_all_sql(); ?>
	          	 	</span>
	          </div> 	
		<?php 
	}

	public function location_piker_setting_callback(){


              require_once plugin_dir_path( __FILE__ ) . '/templates/location-piker-setting.php';                    

	}

	public function location_piker_setting_api(){

			/* register_setting( $option_group, $option_name, $sanitize_callback );  */  
          register_setting('location_piker_setting_group','google_map_api_key', array($this,'google_map_api_key_sanitize'));
          add_settings_section( 'location_piker_setting_section', __( 'General Settings', 'location piker' ),array($this,'location_piker_setting_section_cb'), 'location_piker_setting' );
          add_settings_field('google_map_api_key' ,  // $id
                            __( 'Google Map Api Key', 'location piker' ), // $title
                               array($this,'google_map_api_key'),  // $callback
                              'location_piker_setting', // $page
                              'location_piker_setting_section',// $section
                               array( 'id' => 'professional_title', // $args 
                                     'class'=>'form-group',
                                     'type' => 'text' , 
                                     'name'=>'google_map_api_key' ));

	}

	public function location_piker_setting_section_cb(){

		

	}

	public function google_map_api_key( $args ){

      $value = esc_attr(get_option($args['name']));
      $value = str_replace("@"," ",$value);      
      $output = sprintf( '<input id="%1s" 
                                 class = "regular-text" 
                                 type = "%2s"  
                                 name ="%3s" 
                                 value = "%4s" ',$args['id'],$args['type'],$args['name'],$value );
      echo $output;  
		
	}

	public function google_map_api_key_sanitize( $input ){

	  $output = sanitize_text_field( $input );
      return $output;
		
	}

}