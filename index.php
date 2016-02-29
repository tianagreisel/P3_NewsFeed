<?php

/**
* index.php a list page of news.  Shows the different categories for the news feeds stored in  * a database.  User clicks on a category and it will take them to the subcategory view page
* for that category they clicked on.
*
*  based on demo_shared.php
*
 * demo_idb.php is both a test page for your IDB shared mysqli connection, and a starting point for 
 * building DB applications using IDB connections
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

# '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials

$config->titleTag = smartTitle(); #Fills <title> tag. If left empty will fallback to $config->titleTag in config_inc.php
$config->metaDescription = smartTitle() . ' - ' . $config->metaDescription; 


//if the session is not started, start the session (throws warning if session already started)
if (!isset($_SESSION)){
    session_start();
}

//make a session variable for the start of the session
if(!isset($_SESSION['start_time'])) {
$_SESSION['start_time'] = new DateTime('now');
}

# SQL statement - selects everything from categories
$sql = "select * from wn16_categories";

//END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to header_inc.php
?>

<h1 align="center">News</h1>

<h3 align="left">Categories</h3>

<?php

#IDB::conn() creates a shareable database connection via a singleton class
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

//goes through all the records in the database
if(mysqli_num_rows($result) > 0)
{#there are records - present data
	while($row = mysqli_fetch_assoc($result)) //while more rows
	{# pull data from associative array
	   echo '<p>';
        //create link to subcategory view page with category name, send CategoryID along on query string
	  echo '<a href="p3_subcategory_view.php?id=' . $row['CategoryID'] . '">' . $row['Name'] . '</a><br />'; 
        //output description of category
	   echo 'Description: <b>' . $row['Description'] . '</b><br />';
	   echo '</p>';
	}
}else{#no records
	echo '<div align="center">Sorry, there are no records that match this query</div>';
}

@mysqli_free_result($result);

get_footer(); #defaults to footer_inc.php

 

?>