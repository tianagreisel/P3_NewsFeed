<?php

/**
*  p3_subcategory_view.php a view page to show the 3 subcategories of a single category
* the user clicked on.  The user can click on one of the subcategories and it will take
* them to a news feed page representing a single news feed (rss feed) for the subcategory
* they clicked on.
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

# '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials

$config->titleTag = smartTitle(); #Fills <title> tag. If left empty will fallback to $config->titleTag in config_inc.php
$config->metaDescription = smartTitle() . ' - ' . $config->metaDescription; 

//if the category id was sent along the query string and it is a number greater than 0
if(isset($_GET['id']) && (int)$_GET['id'] > 0)
{//good data, process!
    
    //store the id sent along the query string in a variable $id
    $id = (int)$_GET['id'];

}
else{//bad data

    //this is redirection to index.php
    header('Location:index.php');
}


//SQL statement
//inner join subcategories and categories table to get name of category to display on 
//subcategories view page for clarification
$sql = "select s.SubcategoryID, c.Name as CategoryName from wn16_subcategories s inner join wn16_categories c on 
s.CategoryID = c.CategoryID where s.CategoryID=$id";

//if 'current_catgoryID' is not set in $_SESSION, store the current category number in it
if(!isset($_SESSION['current_categoryID'])){
    $_SESSION['current_categoryID'] = $id;  //store the current CategoryID number
    
}

//set current category ID equal to $id and store in $_SESSION
$_SESSION['current_categoryID'] = $id;  //store the current CategoryID number

?>

<?php
#IDB::conn() creates a shareable database connection via a singleton class
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

//create an array to store subcategory objects
$subCategoryObjects= array();

//go through the rows in the database that match sql query
if(mysqli_num_rows($result) > 0)
{#there are records - present data
	while($row = mysqli_fetch_assoc($result))
	{# pull data from associative array
    
     //set $id variable equal to the subcategory id in database
     $id = $row['SubcategoryID'];
    
     //create a subcategory object with the subcategory id from database
     $subCategoryObjects[] = new Subcategory($id);
      
     //set category name to current cateogry in order to display in heading
     $category = $row['CategoryName']; //category name to display with subcategories
        
	   
	}
    
}else{#no records
	echo '<div align="center">Sorry, there are no records that match this query</div>';
}

@mysqli_free_result($result);


//if 'subcategory_objects' no set in $_SESSSION, create it and store array of subcategory objects in it
if(!isset($_SESSION['subcategory_objects'])){
    
    $_SESSION['subcategory_objects'] = $subCategoryObjects;
}

//update subcategory objects stored in session super global to current subcategories
$_SESSION['subcategory_objects'] = $subCategoryObjects;

//END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to header_inc.php

?>

<h3 align="left">Subcategories</h3>

<?php

//iterte through subcategory objects in array
foreach ($subCategoryObjects as $object){
    
    //use the subcategory object getLink() method to display link to the news feed page
    //along with a description of the subcategory
    $object->getLink();
}

//back button to go back to index page (category list page)
echo '<p><a href="index.php">BACK</a></p>';

get_footer(); #defaults to footer_inc.php




