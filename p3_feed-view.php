<?php

include 'NewsFeed.php';

/**
*  survey_view1.php a view page to show a single survey
*
*  based on demo_shared.php
*
 * demo_idb.php is both a test page for your IDB shared mysqli connection, and a starting point for 
 * building DB applications using IDB connections
 *
 * @package nmCommon
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 2.09 2011/05/09
 * @link http://www.newmanix.com/
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License ("OSL") v. 3.0
 * @see config_inc.php  
 * @see header_inc.php
 * @see footer_inc.php 
 * @todo none
 */
# '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials

$config->titleTag = smartTitle(); #Fills <title> tag. If left empty will fallback to $config->titleTag in config_inc.php
$config->metaDescription = smartTitle() . ' - ' . $config->metaDescription; 
/*
$config->metaDescription = 'Web Database ITC281 class website.'; #Fills <meta> tags.
$config->metaKeywords = 'SCCC,Seattle Central,ITC281,database,mysql,php';
$config->metaRobots = 'no index, no follow';
$config->loadhead = ''; #load page specific JS
$config->banner = ''; #goes inside header
$config->copyright = ''; #goes inside footer
$config->sidebar1 = ''; #goes inside left side of page
$config->sidebar2 = ''; #goes inside right side of page
$config->nav1["page.php"] = "New Page!"; #add a new page to end of nav1 (viewable this page only)!!
$config->nav1 = array("page.php"=>"New Page!") + $config->nav1; #add a new page to beginning of nav1 (viewable this page only)!!
*/

if(isset($_GET['id']) && (int)$_GET['id'] > 0)
{//good data, process!
    
    $id = (int)$_GET['id'];
    
    //store feedID of current feed object
    $_SESSION['current_feedID'] = $id;

}
else{//bad data, you go away now!

    //this is redirection in PHP:
    header('Location:index.php');
}



//END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to header_inc.php
?>
<h2 align="center">News Feed</h2>



<?php

//SESSION AREA
//giving error that $_SESSION['feed_time'] is not an array so unset 
/*if(!is_array($_SESSION['feed_time'])){
    session_unset();
    session_destroy();
    session_start();
*/

$myFeed = ""; //initialize Feed

//if $_SESSION['feed_time'] not set
if(!isset($_SESSION['feed_objects'])){
    
    //$_SESSION['feed_time'] = $myFeed->timeCreated;
    $_SESSION['feed_objects'] = array();
    $myFeed = new Feed($id);
    $feedObjectTitle = $myFeed->Title;
    $_SESSION['feed_objects']["$feedObjectTitle"] = $myFeed;
    
    

    
}



//store feedID of current feed object
//$_SESSION['current_feedID'] = $id;
//var_dump($_SESSION['current_feedID']);

//if $_SESSION['feed_time'] is set
if(isset($_SESSION['feed_objects'])){
    //$feedObjects[] = $_SESSION['feed_time'][];

   
        foreach ($_SESSION['feed_objects'] as $feed){ //loop through all cached feed objects
            
           // var_dump($feed);
           // $feedObject = $feed;
          //echo "$feed->Title";
            //echo "$feed->SubcategoryID";
            if($feed->SubcategoryID == $_SESSION['current_feedID'] )//find cached feed object that matches user selection
            {
               $feedObject = $feed;  //set $myFeed equal to store cached feedObject to get news
                
                //get current time
                //$current_time = date('Y-m-d H:i:s');  
                $current_time = new DateTime('now');
                $feedObject_time_created = $feedObject->timeCreated;  //get time feed object cached
                $diff = date_diff($current_time, $feedObject_time_created);  //get difference
                $formatedDiff = $diff->format('%i');  //format difference
                if($formatedDiff > 2 ) {  //if cached feed more than 15 minutes old
                    
                    $myFeed = new Feed($id);  //create new feed object of that subcategory
                    //$feed = $myFeed;   //set session cache to new object
                    $feedObjectTitle = $myFeed->Title;  //get title of feed object for associative array stored in session to reference feed category
                    $_SESSION['feed_objects']["$feedObjectTitle"] = $myFeed;
                }
                
                $myFeed = $feed; //cached feed object less than 15 minutes old, use to display feed
                    
                
                
                
                                    //feed from cached feed object of that category
                                    //get feed object, check timestamp to make sure not more than 15 minutes old
                                    //if less than 15 minutes old, use cached feed object for feed
                                    //else, create new feed object with new database query
            }
            else{ //feed object doesn't exist for category, create it and cache it
            //put current feed object into $_SESSION if feed object for category doesn't exist
            $myFeed = new Feed($id);
          
            $feedObjectTitle = $myFeed->Title;
            $_SESSION['feed_objects']["$feedObjectTitle"] = $myFeed;
            }
        }
    
    
}

 //session_unset($_SESSION['feed_objects']);
var_dump($_SESSION['feed_objects']);
//var_dump($_SESSION['feed_time']); 

$time = $myFeed->timeCreated;
$feed_time_to_display = $time->format('Y-m-d H:i:s');
?>
<h6><?=$feed_time_to_display?></h6>
<?php



//END SESSION AREA

//GET FROM SESSION
//$myFeed = new Feed($id);


//$feedTitle = $myFeed->Title;

//echo "$feedTitle";


//if(!isset($_SESSION["$feedTitle"])){
   // $_SESSION["$feedTitle"] = $myFeed;
//}
//session_unset($_SESSION);
//var_dump($_SESSION);

$myFeed->getFeedTitle(); //use the Feed objects getFeedTitle() to display the subcategory 
                            //to create heading of subcategory news being displayed

$request = $myFeed->URL;
//$request = "https://news.google.com/news?cf=all&hl=en&pz=1&ned=us&topic=tc&output=rss";
//$request = "http://news.google.com/news?cf=all&hl=en&pz=1&ned=us&q=Albert+Einstein&output=rss";
  $response = file_get_contents($request);
  $xml = simplexml_load_string($response);
 //print '<h1>' . $xml->channel->title . '</h1>';

//$newsFeedObjects = array();

foreach($xml->channel->item as $story)
  {
    echo '<a href="' . $story->link . '">' . $story->title . '</a><br />'; 
    echo '<p>' . $story->description . '</p><br /><br />';
  }


    
/*$time1 = $_SESSION['start_time'];
$time2 = $_SESSION['feed_time'];
$diff = date_diff($time1, $time2);
$formatedDiff = $diff->format('%i');//format time difference into minutes and seconds

//echo "$formatedDiff";
if($formatedDiff > 15){
    
    //var_dump($time_in_session);
    echo 'Your session has timed out';
} //END SESSION AREA*/
/*foreach($xml->channel->item as $story)
  {
    $newsFeedObjects[] = new NewsFeed($story);
}

foreach($newsFeedObjects as $feed){
    
    $feed->getFeed();
}*/


//dumpDie($mySurvey);  //dump die is var_dump and die function Bill built for us
$categoryID = $_SESSION['current_categoryID'];
echo '<p><a href="p3_subcategory_view.php?id=' . $categoryID. '">BACK</a></p>';

get_footer(); #defaults to footer_inc.php


class Feed
{

    public $SubcategoryID = 0;
    
    public $Title = '';
    
    public $Description = '';
    
    public $URL = '';
    
    public $timeCreated = 0;
    
    public function __construct($id)
    {
    
        $this->SubcategoryID = (int)$id;
        $this->timeCreated = new DateTime('now');
        //$this->timeCreated = date('Y-m-d H:i:s');
    
        # SQL statement - PREFIX is optional way to distinguish your app
        $sql = "select * from wn16_feeds where SubcategoryID=$this->SubcategoryID";
    
        #IDB::conn() creates a shareable database connection via a singleton class
        $result = mysqli_query(IDB::conn(),$sql) or                                                 die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));


        if(mysqli_num_rows($result) > 0)
        {#there are records - present data
	       while($row = mysqli_fetch_assoc($result))
            {# pull data from associative array
	 
            $this->Title = $row['Title'];
            $this->Description = $row['Description'];
               $this->URL = $row['URL'];

	   }
        }

        

        @mysqli_free_result($result);
    
    
    }#end Feed constructor 
    
    public function getFeedTitle(){
        
        echo '
        <h3 align="left">' . $this->Title . ' News</h3>
        
        ';
        
        
        
    }
  
}
