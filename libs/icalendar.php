<?php
require_once get_template_directory().'/libs/resources/class.iCalReader.php';

function wpestate_clear_ical_imported($prop_id){
    $reservation_array = get_post_meta($prop_id, 'booking_dates',true);
    if(!is_array($reservation_array)){
        $reservation_array=array();
    }
    
    foreach($reservation_array as $key=>$value){
        if (is_numeric($value)==0){
            unset($reservation_array[$key]);
        }
    }
    update_post_meta($prop_id, 'booking_dates',$reservation_array);
    
}


function wpestate_import_calendar_feed_listing_global($prop_id){
    $property_icalendar_import_multi =   get_post_meta($prop_id, 'property_icalendar_import_multi', true);
    foreach( $property_icalendar_import_multi as $key=>$feed_data){
       wpestate_import_calendar_feed_listing($prop_id,$feed_data['feed'],$feed_data['name']);
    }
}




function wpestate_import_calendar_feed_listing($prop_id,$property_icalendar_import,$name){
   
  
    if(!intval($prop_id)){
        exit();
    }
   
    if($property_icalendar_import ==''){
        return;
    }
    if (filter_var($property_icalendar_import, FILTER_VALIDATE_URL) === FALSE) {
       return;
    }
    
    
    $ical   = new ICal($property_icalendar_import);
    
    $events = $ical->events();
    $date = $events[0]['DTSTART'];
    

   
    if (!is_array($events)){
        return;
    }
    
   //wpestate_clear_ical_imported($prop_id);
    $data_to_insert =   array();
    //DTSTART which sets a starting time, and a DTEND which sets an ending time.
    foreach ($events as $event) {
        $unix_time_start    ='';
        $unix_time_end      ='';
        if( isset($event['UID']) ){
            $uid                =$event['UID'];
            
        }else{
            $uid=   esc_html__('external','wpestate');
        }
        
        
        if( isset($event['DTSTART']) ){
            //  echo "DTSTART: ".$event['DTSTART']." - UNIX-Time: ".$ical->iCalDateToUnixTimestamp($event['DTSTART'])."<br/>";
            $unix_time_start =$ical->iCalDateToUnixTimestamp($event['DTSTART']);
        }

        if( isset($event['DTEND']) ){
            //echo "DTEND: ".$event['DTEND']."<br/>";
            $unix_time_end =$ical->iCalDateToUnixTimestamp($event['DTEND']);
       }

        /*
        if( isset($event['SUMMARY']) ){
         echo "SUMMARY: ".$event['SUMMARY']."<br/>";
        }

        if( isset($event['DTSTAMP']) ){
            echo "DTSTAMP: ".$event['DTSTAMP']."<br/>";
        }

        if( isset($event['UID']) ){
            echo "UID: ".$event['UID']."<br/>";
        }

        if( isset($event['CREATED']) ){
            echo "CREATED: ".$event['CREATED']."<br/>";
        }

        if( isset($event['DESCRIPTION']) ){
        echo "DESCRIPTION: ".$event['DESCRIPTION']."<br/>";
        }

        if( isset($event['LAST-MODIFIED']) ){
            echo "LAST-MODIFIED: ".$event['LAST-MODIFIED']."<br/>";
        }

        if( isset($event['LOCATION']) ){
            echo "LOCATION: ".$event['LOCATION']."<br/>";
        }

        if( isset($event['SEQUENCE']) ){
            echo "SEQUENCE: ".$event['SEQUENCE']."<br/>";
        }

        if( isset($event['STATUS']) ){
            echo "STATUS: ".$event['STATUS']."<br/>";
        }

        if( isset($event['TRANSP']) ){
            echo "TRANSP: ".$event['TRANSP']."<br/>";
        }

        echo "<hr/>";
        
        */
       
        // print $unix_time_start." / ".$unix_time_end." / ".$uid." </br>";
        $uid            =   $name;// update on 1.20 with multuple ical feed
        $temp_array     =   array();
       
        
        if( $unix_time_start!='' && $unix_time_end!='' && $uid !=''){
            // wpestate_insert_booking_external_event($prop_id, $unix_time_start,$unix_time_end,$uid);
            $temp_array  =   array(); 
            $temp_array['prop_id']          =   $prop_id;
            $temp_array['unix_time_start']  =   $unix_time_start;
            $temp_array['unix_time_end']    =   $unix_time_end;
            $temp_array['uid']              =   $uid;
            $data_to_insert[]               =   $temp_array;
        }
        
        
    }
    
    $reservation_array  = get_post_meta($prop_id, 'booking_dates',true);
    $dates_with_uid     = array_keys( $reservation_array, $data_to_insert[0]['uid'] );
    
    foreach ($dates_with_uid as $key=>$timestamp){
        unset($reservation_array[$timestamp]);
    }
    update_post_meta($prop_id, 'booking_dates',$reservation_array);
    
    
    foreach ($data_to_insert as $key=>$to_insert){
        wpestate_insert_booking_external_event($to_insert['prop_id'], $to_insert['unix_time_start'], $to_insert['unix_time_end'], $to_insert['uid'] );
    }
    
}




function  wpestate_insert_booking_external_event($prop_id, $unix_time_start,$unix_time_end,$uid){
    //print  gmdate("Y-m-d\TH:i:s\Z", $unix_time_start).''.gmdate("Y-m-d\TH:i:s\Z", $unix_time_end).'---.'.$uid.'</br>';
    
    $converted_start_date       =   gmdate("Y-m-d 0:0:0", $unix_time_start);
    $convert_unix_time_start    =   strtotime($converted_start_date);
    $converted_end_date         =   gmdate("Y-m-d 0:0:0", $unix_time_end);
    $convert_unix_time_end      =   strtotime($converted_end_date);
    
    //print  gmdate("Y-m-d\TH:i:s\Z", $convert_unix_time_start).'---'.gmdate("Y-m-d\TH:i:s\Z", $convert_unix_time_end).'</br>';
    //print $convert_unix_time_start.'/'.$unix_time_start.' --- '.$convert_unix_time_end.' / '.$unix_time_end.'</br>';
    
    
    $unix_time_start=$convert_unix_time_start;
    $unix_time_end=$convert_unix_time_end;
     
    $now=time();
    $daysago = $now-3*24*60*60;
    
    if ($unix_time_end<$daysago){
        return;
    }
    
    $reservation_array  = get_post_meta($prop_id, 'booking_dates',true);
  
     
    if(!is_array($reservation_array)){
        $reservation_array=array();
    }
    
    
    $unix_time_start    = gmdate("Y-m-d\TH:i:s\Z", $unix_time_start);
    $unix_time_end      = gmdate("Y-m-d\TH:i:s\Z", $unix_time_end);
    
    $from_date      =   new DateTime($unix_time_start);
    $from_date_unix =   $from_date->getTimestamp();
    $to_date        =   new DateTime($unix_time_end);
    $to_date_unix   =   $to_date->getTimestamp();
            
    if( is_numeric($uid)){
        $uid=(string)$uid.' ';
    } 
    
  
    $reservation_array[$from_date_unix] =   $uid;
    $from_date_unix                     =   $from_date->getTimestamp();
    
    while ($from_date_unix < $to_date_unix){
        if( is_numeric($uid)){
            $uid=(string)$uid.' ';
        }
        $reservation_array[$from_date_unix]     =   $uid;
        $from_date->modify('tomorrow');
        $from_date_unix =   $from_date->getTimestamp();
    }
    
    
    update_post_meta($prop_id, 'booking_dates',$reservation_array);
 
}


function wpestate_update_calendar_missing_dates($reservation_array,$to_compare_array){
    $result = array_keys( $reservation_array, "air" );

    
    
}
