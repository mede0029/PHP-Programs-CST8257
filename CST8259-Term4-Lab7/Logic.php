<?php
//THIS FILE CONTAINS THE LOGIC RUNNING BEHIND THE INDEX PAGE
session_start();
define ("RESTAURANT_REVIEWS_PATH", "restaurant_reviews.xml"); //creating a path for the file address
$restaurants = simplexml_load_file(RESTAURANT_REVIEWS_PATH); //loading file into variable "restaurants"
extract ($_GET); //extracting all values from form

//retrieving code in JSON format:
$data = file_get_contents('php://input');
if ($data)
{
    $postdata = json_decode($data, true);
}

//DISPLAY RESTAURANT NAMES:
//if nothing or if "selec one..." is selected from dropdown list
if (!isset($restaurantDropDown) || $restaurantDropDown == -1 )
{
    $restaurantDropDown = -1; //set dropdown list to "select one..."
    $address = ""; //clear address field
    $summary = ""; //clear summary field
    $rating = 1; //add 1 to rating
      
}
else //if something is selected
{
    //creating a variable with the selected restaurant, using dropdown value as index
    $selectedRestaurant = $restaurants->restaurant[intval($restaurantDropDown)];
    
    //clicking on save changes button
    if (isset($postdata["saveChanges"]))
    {
        $selectedRestaurant->summary = $postdata["summary"]; //update summary on variable with value from JSON
        $selectedRestaurant->rating = $postdata["rating"]; //update rating on variable with value from JSON
        $restaurants->asXML(RESTAURANT_REVIEWS_PATH); //save xml  
    }

    //displaying info for restaurant from xml
    $data = new stdClass();
    $data->addressLbl = true;
    $data->address = $selectedRestaurant->address->street . ", "
              .$selectedRestaurant->address->city . ", "
              .$selectedRestaurant->address->province . ", "
              .$selectedRestaurant->address->postalCode;    
    $data->summary = (string)$selectedRestaurant->summary;
    $data->rating = intval($selectedRestaurant->rating);    
}

//updating information for JSON element
echo json_encode($data);
?>
