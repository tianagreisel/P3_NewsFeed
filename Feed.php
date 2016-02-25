<?php
/**
* Feed.php stores the class Feed which is used to create objects the news feed application 
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
* This class creates Item objects, which have a name, a description, and a price associated
* with each item.  The item can be anything, but for the foodTruck application it is used
* in it represents items in a food truck.
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
