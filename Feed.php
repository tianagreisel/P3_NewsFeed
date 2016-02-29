<?php
/**
* Feed.php stores the class Feed which is used to create objectsfor the news feed application 
* in the wn16 project.
*
* @package
* @subpackage
* @author Tiana Greisel
* @version 1.0 2016/02/17
* @link 
* @license
* @todo none
*/

/**
* This class creates Feed objects, which have a subcategoryID, a title, a description, a URL, and a 
* time of creation for each feed object associated with a rss news feed.
*
* @todo none
*
*/
class Feed
{

    public $SubcategoryID = 0;
    
    public $Title = '';
    
    public $Description = '';
    
    public $URL = '';
    
    public $timeCreated = 0;
    
    /**
    * This function constructs a new feed object and sets the subcategoryID, timeCreated,
    * title, url, and description of the feed object.
    *
    * @ param $id id of feed object as stored in database
    * @ return none
    */
    public function __construct($id)
    {
    
        //set subcategoryID to id paramenter
        $this->SubcategoryID = (int)$id;
        
        //set time created to now
        $this->timeCreated = new DateTime('now');
    
        # SQL statement, get eveything from feeds page where subcategoryID = id
        $sql = "select * from wn16_feeds where SubcategoryID=$this->SubcategoryID";
    
        #IDB::conn() creates a shareable database connection via a singleton class
        $result = mysqli_query(IDB::conn(),$sql) or                                                 die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

        //go through all rows matching query
        if(mysqli_num_rows($result) > 0)
        {#there are records - present data
        
	       while($row = mysqli_fetch_assoc($result))  //while more data
            {# pull data from associative array
	 
            //set title to Title as stored in database
            $this->Title = $row['Title'];
            
            //set description to Description stored in database
            $this->Description = $row['Description'];
            
            //set URL to url stored for rss news feed stored in database
            $this->URL = $row['URL'];

	       }
            }

        @mysqli_free_result($result);
    
    
    }#end Feed constructor 
    
    /*
    * This function creates an <h3> html tag to display the title of the feed.
    *
    *
    * @param none
    * @return none
    */
    public function getFeedTitle(){
        
        echo '
        
        <h3 align="left">' . $this->Title . ' News</h3>
        
        '; 
    }
}

