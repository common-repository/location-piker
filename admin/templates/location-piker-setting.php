<?php
settings_errors();
echo '<form method="post" action="options.php" id="">';                   
settings_fields('location_piker_setting_group');                    
do_settings_sections( 'location_piker_setting' );            
submit_button();
echo '</form>';