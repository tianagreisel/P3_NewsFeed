/*
  wn16_newsFeed2.sql - first version of SurveySez application
  
  Here are a few notes on things below that may not be self evident:
  
  INDEXES: You'll see indexes below for example:
  
  INDEX SurveyID_index(SurveyID)
  
  Any field that has highly unique data that is either searched on or used as a join should be indexed, which speeds up a  
  search on a tall table, but potentially slows down an add or delete
  
  TIMESTAMP: MySQL currently only supports one date field per table to be automatically updated with the current time.  We'll use a 
  field in a few of the tables named LastUpdated:
  
  LastUpdated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP
  
  The other date oriented field we are interested in, DateAdded we'll do by hand on insert with the MySQL function NOW().
  
  CASCADES: In order to avoid orphaned records in deletion of a Survey, we'll want to get rid of the associated Q & A, etc. 
  We therefore want a 'cascading delete' in which the deletion of a Survey activates a 'cascade' of deletions in an 
  associated table.  Here's what the syntax looks like:  
  
  FOREIGN KEY (SurveyID) REFERENCES wn16_surveys(SurveyID) ON DELETE CASCADE
  
  The above is from the Questions table, which stores a foreign key, SurveyID in it.  This line of code tags the foreign key to 
  identify which associated records to delete.
  
  Be sure to check your cascades by deleting a survey and watch all the related table data disappear!
  
  
*/


SET foreign_key_checks = 0; #turn off constraints temporarily

#since constraints cause problems, drop tables first, working backward
DROP TABLE IF EXISTS wn16_categories;
DROP TABLE IF EXISTS wn16_subcategories;
DROP TABLE IF EXISTS wn16_feeds;
  
#all tables must be of type InnoDB to do transactions, foreign key constraints
CREATE TABLE wn16_categories(
CategoryID INT UNSIGNED NOT NULL AUTO_INCREMENT,
Name VARCHAR(255) DEFAULT '',
Description TEXT DEFAULT '',
DateAdded DATETIME,
LastUpdated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (CategoryID)
)ENGINE=INNODB; 

#assigning first survey to AdminID == 1
INSERT INTO wn16_categories VALUES (NULL,'Science','All about Science',NOW(),NOW()); 

#foreign key field must match size and type, hence SurveryID is INT UNSIGNED
CREATE TABLE wn16_subcategories(
SubcategoryID INT UNSIGNED NOT NULL AUTO_INCREMENT,
CategoryID INT UNSIGNED DEFAULT 0,
Name TEXT DEFAULT '',
Description TEXT DEFAULT '',
DateAdded DATETIME,
LastUpdated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (SubcategoryID),
INDEX CategoryID_index(CategoryID),
FOREIGN KEY (CategoryID) REFERENCES wn16_categories(CategoryID) ON DELETE CASCADE
)ENGINE=INNODB;

INSERT INTO wn16_subcategories VALUES (NULL,1,'NASA','All about NASA',NOW(),NOW());
INSERT INTO wn16_subcategories VALUES (NULL,1,'Albert Einstein','All about Albert Einstein',NOW(),NOW());
INSERT INTO wn16_subcategories VALUES (NULL,1,'Antarctica','All about Antarctica',NOW(),NOW());

CREATE TABLE wn16_feeds(
FeedID INT UNSIGNED NOT NULL AUTO_INCREMENT,
CategoryID INT UNSIGNED DEFAULT 0,
SubcategoryID INT UNSIGNED DEFAULT 0,
Title TEXT DEFAULT '',
Description TEXT DEFAULT '',
URL TEXT DEFAULT '',
DateAdded DATETIME,
LastUpdated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (FeedID),
INDEX CategoryID_index(CategoryID),
INDEX SubcategoryID_index(SubcategoryID),
FOREIGN KEY (CategoryID) REFERENCES wn16_categories(CategoryID) ON DELETE CASCADE,
FOREIGN KEY (SubcategoryID) REFERENCES wn16_subcategories(SubcategoryID) ON DELETE CASCADE
)ENGINE=INNODB;

INSERT INTO wn16_feeds VALUES (NULL, 1, NULL, 'Science', 'This is science news','https://news.google.com/news/section?cf=all&pz=1&ned=us&topic=snc&siidp=c0bbb989fcd4e9564b5df9b8104b88c48465&ict=ln',NOW(),NOW());
INSERT INTO wn16_feeds VALUES (NULL, 1, 1, 'NASA','This is NASA News','http://news.google.com/news?cf=all&hl=en&pz=1&ned=us&q=NASA&output=rss', NOW(),NOW());
INSERT INTO wn16_feeds VALUES (NULL, 1, 2, 'Albert Einstein','This is Albert Einstein News','http://news.google.com/news?cf=all&hl=en&pz=1&ned=us&q=Albert+Einstein&output=rss', NOW(),NOW());
INSERT INTO wn16_feeds VALUES (NULL, 1, 3, 'Antarctica','This Antarctica News','http://news.google.com/news?cf=all&hl=en&pz=1&ned=us&q=Antarctica&output=rss', NOW(),NOW());



/*
Add additional tables here


*/
SET foreign_key_checks = 1; #turn foreign key check back on