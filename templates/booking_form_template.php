<?php 
global $post;
global $current_user;
global $feature_list_array;
global $propid ;
global $post_attachments;
global $options;
global $where_currency;
global $property_description_text;     
global $property_details_text;
global $property_features_text;
global $property_adr_text;  
global $property_price_text;   
global $property_pictures_text;    
global $propid;
global $gmap_lat;  
global $gmap_long;
global $unit;
global $currency;
global $use_floor_plans;
global $favorite_text;
global $favorite_class;
global $property_action_terms_icon;
global $property_action;
global $property_category_terms_icon;
global $property_category;
global $guests;
global $bedrooms;
global $bathrooms;
global $show_sim_two;
global $guest_list;
global $post_id;

?>

<div class="listing_main_image_price">
    <?php  
    $price_per_guest_from_one       =   floatval( get_post_meta($post->ID, 'price_per_guest_from_one', true) ); 
    $price                          =   floatval( get_post_meta($post->ID, 'property_price', true) );
    wpestate_show_price($post->ID,$currency,$where_currency,0); 
    if($price!=0){
        if( $price_per_guest_from_one == 1){
            echo ' '.esc_html__( 'per guest','wpestate'); 
        }else{
            echo ' '.esc_html__( 'per night','wpestate'); 
        }
    }
    ?>
</div>
      
<div class="booking_form_request" id="booking_form_request">
    <div id="booking_form_request_mess"></div>
    <h3><?php esc_html_e('Book Now','wpestate');?></h3>
             
    <div class="has_calendar calendar_icon">
        <input type="text" id="start_date" placeholder="<?php esc_html_e('Check in','wpestate'); ?>"  class="form-control calendar_icon" size="40" name="start_date" 
                value="<?php if( isset($_GET['check_in_prop']) ){
                   echo sanitize_text_field ( $_GET['check_in_prop'] );
                }
                ?>">
    </div>

    <div class=" has_calendar calendar_icon">
        <input type="text" id="end_date" disabled placeholder="<?php esc_html_e('Check Out','wpestate'); ?>" class="form-control calendar_icon" size="40" name="end_date" 
               value="<?php if( isset($_GET['check_out_prop']) ){
                   echo sanitize_text_field ( $_GET['check_out_prop'] );
                }
                ?>">
    </div>

    <div class=" has_calendar guest_icon ">
        <?php 
        $max_guest = get_post_meta($post_id,'guest_no',true);
        print '
        <div class="dropdown form-control">
            <div data-toggle="dropdown" id="booking_guest_no_wrapper" class="filter_menu_trigger" data-value="';
            if(isset($_GET['guest_no_prop']) && $_GET['guest_no_prop']!=''){
                echo esc_html( $_GET['guest_no_prop'] );
            }else{
              echo 'all';
            }


            print '">';
            print '<div class="text_selection">';
            if(isset($_GET['guest_no_prop']) && $_GET['guest_no_prop']!=''){
                echo esc_html( $_GET['guest_no_prop'] ).' '.esc_html__( 'guests','wpestate');
            }else{
                esc_html_e('Guests','wpestate');
            }
            print '</div>';

            print'<span class="caret caret_filter"></span>
            </div>           
            <input type="hidden" name="booking_guest_no"  value="">
            <ul  class="dropdown-menu filter_menu" role="menu" aria-labelledby="booking_guest_no_wrapper" id="booking_guest_no_wrapper_list">
                '.$guest_list.'
            </ul>        
        </div>';
        ?> 
    </div>
                
            
    <?php
    // shw extra options
    wpestate_show_extra_options_booking($post_id)
    ?>
            

    <p class="full_form " id="add_costs_here"></p>            

    <input type="hidden" id="listing_edit" name="listing_edit" value="<?php echo $post_id;?>" />

    <div class="submit_booking_front_wrapper">
        <?php   
        $overload_guest                 =   floatval   ( get_post_meta($post_id, 'overload_guest', true) );
        $price_per_guest_from_one       =   floatval   ( get_post_meta($post_id, 'price_per_guest_from_one', true) );
        ?>

        <?php  $instant_booking                 =   floatval   ( get_post_meta($post_id, 'instant_booking', true) ); 
        if($instant_booking ==1){ ?>
            <div id="submit_booking_front_instant_wrap"><input type="submit" id="submit_booking_front_instant" data-maxguest="<?php echo $max_guest; ?>" data-overload="<?php echo $overload_guest;?>" data-guestfromone="<?php echo $price_per_guest_from_one; ?>"  class="wpb_btn-info wpb_btn-small wpestate_vc_button  vc_button" value=" <?php esc_html_e('Instant Booking','wpestate');?>" /></div>
        <?php }else{?>   
            <input type="submit" id="submit_booking_front" data-maxguest="<?php echo $max_guest; ?>" data-overload="<?php echo $overload_guest;?>" data-guestfromone="<?php echo $price_per_guest_from_one; ?>"  class="wpb_btn-info wpb_btn-small wpestate_vc_button  vc_button" value="<?php esc_html_e('Book Now','wpestate');?>" />
        <?php }?>


        <?php wp_nonce_field( 'booking_ajax_nonce', 'security-register-booking_front' );?>
    </div>

    <div class="third-form-wrapper">
        <div class="col-md-6 reservation_buttons">
            <div id="add_favorites" class=" <?php print $favorite_class;?>" data-postid="<?php the_ID();?>">
                <?php echo $favorite_text;?>
            </div>                 
        </div>

        <div class="col-md-6 reservation_buttons">
            <div id="contact_host" class="col-md-6"  data-postid="<?php the_ID();?>">
                <?php esc_html_e('Contact Owner','wpestate');?>
            </div>  
        </div>
    </div>

    <?php 
    if (has_post_thumbnail()){
        $pinterest = wp_get_attachment_image_src(get_post_thumbnail_id(),'wpestate_property_full_map');
    }
    ?>

    <div class="prop_social">
        <span class="prop_social_share"><?php esc_html_e('Share','wpestate');?></span>
        <a href="http://www.facebook.com/sharer.php?u=<?php echo esc_url(get_permalink()); ?>&amp;t=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="share_facebook"><i class="fa fa-facebook fa-2"></i></a>
        <a href="http://twitter.com/home?status=<?php echo urlencode(get_the_title() .' '.esc_url( get_permalink()) ); ?>" class="share_tweet" target="_blank"><i class="fa fa-twitter fa-2"></i></a>
        <a href="https://plus.google.com/share?url=<?php echo esc_url(get_permalink()); ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" target="_blank" class="share_google"><i class="fa fa-google-plus fa-2"></i></a> 
        <?php if (isset($pinterest[0])){ ?>
            <a href="http://pinterest.com/pin/create/button/?url=<?php echo esc_url(get_permalink()); ?>&amp;media=<?php echo $pinterest[0];?>&amp;description=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="share_pinterest"> <i class="fa fa-pinterest fa-2"></i> </a>      
        <?php } ?>           
    </div>             

</div>