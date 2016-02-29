<?php

/**
* Subcategory.php stores the class Subcategory which is used to create subcategory objects 
* used in the news application in the wn16 project.
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
* This class creates Subcategory objects, which have a subcategoryID, a title, a description 
*  and a time of creation for each subcategory object.
*
* @todo none
*
*/
class Subcategory
{

    public $SubcategoryID = 0;
    
    public $Name = '';
    
    public $Description = '';
    
    public $timeCreated;
    
    /**
    * This function constructs a new subcategory object and sets the subcategoryID, timeCreated,
    * name, and description of the subcategory object.
    *
    * @ param $id id of subcategory object as stored in database
    * @ return none
    */
    public function __construct($id)
    {
    
        //sets subcategoryID to id parameter
        $this->SubcategoryID = (int)$id;
        
        //store time Subcategory object created
        $this->timeCreated = new DateTime('now');  
    
        # SQL statement, selects everything from subcategories table where SubcategoryID = id parameter
        $sql = "select * from wn16_subcategories where SubcategoryID=$this->SubcategoryID";
    
        #IDB::conn() creates a shareable database connection via a singleton class
        $result = mysqli_query(IDB::conn(),$sql) or                                                 die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

        //goes through all rows in database matching sql query
        if(mysqli_num_rows($result) > 0)
        {#there are records - present data
	       while($row = mysqli_fetch_assoc($result))  //while more data
            {# pull data from associative array
	 
            //set Name property to Name of subcategory from database
            $this->Name = $row['Name'];
               
            //set Description property to Description of subcategory from database
            $this->Description = $row['Description'];

	       }
        }

        @mysqli_free_result($result);
    
    
    }#end Subcategory constructor 

      /*
    * This function creates a link to the feed view page and sents the id of the subcategory
    * object along the query string. Link is Name of subcategory object and also displays
    * description of subcategory object.
    *
    * @param none
    * @return none
    */
    public function getLink(){
        
        echo '
            <p>
	        <a href="p3_feed-view.php?id=' . $this->SubcategoryID . '">' . $this->Name . '</a><br />
            Description: <b>' . $this->Description . '</b><br />
	   
	       </p>';
        }
        
    }
