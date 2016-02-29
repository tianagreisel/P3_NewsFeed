<?php

/**
*  p3_feed-view.php a view page to show a single news feed for the subcategory clicked on.  
* 
*  based on demo_shared.php
*
* demo_idb.php is both a test page for your IDB shared mysqli connection, and a starting    point for 
 * building DB applications using IDB connections
 *
 *
 * @package 
 * @author Tiana Greisel <tianagreisel@gmail.com>
 * @version 1 2016/02/23
 * @link http://tianagreisel.com/wn16/news/index.php
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License ("OSL") v. 3.0
 * @see config_inc.php  
 * @see header_inc.php
 * @see footer_inc.php 
 * @todo none
 */

include 'Subcategory.php';  //include class of subcategory to make subcategory objects
include 'Feed.php';  //include class of Feed to make feed objects

# '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials

$config->titleTag = smartTitle(); #Fills <title> tag. If left empty will fallback to $config->titleTag in config_inc.php
$config->metaDescription = smartTitle() . ' - ' . $config->metaDescription; 

//if the subcategory id was sent along the query string and it is a number greater than 0
if(isset($_GET['id']) && (int)$_GET['id'] > 0)
{//good data, process!
    
    $id = (int)$_GET['id'];
    
    //store feedID of current feed object
    $_SESSION['current_feedID'] = $id;
    
}
else{//bad data

    //this is redirection to index.php
    header('Location:index.php');
}

//if SESSION variable storing subcategory name isn't set, set the name of current subcategory for the feed
if(!isset($_SESSION['current_subcategory_name'])){
    
    //iterate through all the subcategory objects stored in array in $_SESSION
    foreach($_SESSION['subcategory_objects'] as $subcategoryObject){
        
        //find the subcategory object whose subcategoryID matches current id sent along query string
        if($subcategoryObject->SubcategoryID == $id){
        
            //store the name of the subcategory current id is on in $_SESSION
            $_SESSION['current_subcategory_name'] = $subcategoryObject->Name;
        }
        }
}

//if SESSION variable storing subcategory name is set, then update the name of current subcategory for the feed
if(isset($_SESSION['current_subcategory_name'])){
    
    //iterate through all the subcategory objects stored in array in $_SESSION
    foreach($_SESSION['subcategory_objects'] as $subcategoryObject){
        
        
        //find the subcategory object whose subcategoryID matches current id sent along query string
        if($subcategoryObject->SubcategoryID == $id){
            
            //store the name of the subcategory current id is on in $_SESSION
            $_SESSION['current_subcategory_name'] = $subcategoryObject->Name;
        }
        }
}
//END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to header_inc.php
?>

<h2 align="center">News Feed</h2>

<?php

$myFeed = ""; //initialize Feed

//if $_SESSION['feed_time'] not set
if(!isset($_SESSION['feed_objects'])){
    
    //create an empty array named 'feed_objects' and store in $_SESSION
    $_SESSION['feed_objects'] = array();
    
    //create a new Feed object with the current id
    $myFeed = new Feed($id);
    
    //store the Feed object's title in a variable
    $feedObjectTitle = $myFeed->Title;
    
    //get time of creation from feed object
    $time = $myFeed->timeCreated; 
    
    //store Feed object in an associative array under the feed objects title in $_SESSION
    $_SESSION['feed_objects']["$feedObjectTitle"] = $myFeed;
       
}

//if $_SESSION['feed_time'] is set
if(isset($_SESSION['feed_objects'])){
    
        //loop through all cached feed objects
        foreach ($_SESSION['feed_objects'] as $feed){ 
            
            //find cached feed object that matches user selection
            if($feed->SubcategoryID == $_SESSION['current_feedID'])
            {
                
               //set variable for the $feedObject equal to store cached $feed object to get news
               $feedObject = $feed;  
                
                //get current time 
                $current_time= new DateTime('now');
                
                //get time feed object cached
                $feedObject_time_created = $feedObject->timeCreated; 
                
                //set time to be displayed in heading for when object created
                $time = $feedObject_time_created;  
                
                //get difference of time from now and when feed object created
                $diff = date_diff($current_time, $feedObject_time_created);  
                
                //format difference
                $formatedDiff = $diff->format('%h Hours %i Minutes %s Seconds');
                $hours = $diff->format('%h');  //format difference in hours
                $minutes = $diff->format('%i');  //format difference in minutes
                $seconds = $diff->format('%s');  //format difference in seconds
                
                //convert difference in time to seconds 
                $minutes_to_seconds = $minutes * 60; //convert minutes to seconds
                $diff_in_minutes_as_seconds = $minutes_to_seconds + $seconds; //get total seconds for accuray
                
                //if cached feed more than 15 minutes (900 seconds) old
                if($diff_in_minutes_as_seconds >= 900 ) {  
                    
                    //create new feed object of that subcategory
                    $myFeed = new Feed($id);  
                    
                    //get title of feed object for associative array stored in session to reference feed category
                    $feedObjectTitle = $myFeed->Title;  
                    
                    //store feed object in associative array in $_SESSION
                    $_SESSION['feed_objects']["$feedObjectTitle"] = $myFeed;
                    
                    //set time to be displayed in heading of feed for when object created
                    $time = $myFeed->timeCreated;  
                }
               
                else{ //feed object not more than 15 minutes old
                
                    //cached feed object less than 15 minutes old, use to display feed
                    $myFeed = $feedObject; 
                    
                    //get time created to be displayed for the feed object 
                    $time = $myFeed->timeCreated;  
                }        
            }
        }
    
    //set feed title to name of subcategoryof current subcategory stored in $_SESSION
    $feedTitle = $_SESSION['current_subcategory_name'];
    
    //if feed object not cached in session, create feed object and store in $_SESSION
    if(!isset($_SESSION['feed_objects']["$feedTitle"])){
        
        $myFeed = new Feed($id);  //create new feed object
        $time = $myFeed->timeCreated;  //get time created to be displayed for the feed object 
        
        //store new feed object in associative array by feed title in $_SESSION
        $_SESSION['feed_objects']["$feedTitle"] = $myFeed;
        
    }
    
    
}

//get time from feed object
$feed_time_to_display = $time->format('Y-m-d H:i:s');  //format time to display in <h6> tag below

?>

<h6><?=$feed_time_to_display?></h6>

<?php

//use the Feed objects getFeedTitle() method to display the subcategory 
//to create heading of subcategory news being displayed
$myFeed->getFeedTitle(); 
  
//set request url equal to the url of the rss news feed stored in database
$request = $myFeed->URL;
$response = file_get_contents($request);
$xml = simplexml_load_string($response);

//iterate through xml objects to get story
foreach($xml->channel->item as $story)
  {
    //display the link to the story as a link with story title
    echo '<a href="' . $story->link . '">' . $story->title . '</a><br />'; 
    
    //display description of story
    echo '<p>' . $story->description . '</p><br /><br />';
  }


//store current category id in $_SESSION
$categoryID = $_SESSION['current_categoryID'];

//display back button to subcategory view page
echo '<p><a href="p3_subcategory_view.php?id=' . $categoryID. '">BACK</a></p>';

get_footer(); #defaults to footer_inc.php


