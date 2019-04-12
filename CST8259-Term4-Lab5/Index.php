<?php
session_start();
define ("RESTAURANT_REVIEWS_PATH", "restaurant_reviews.xml"); //creating a path for the file address
$restaurants = simplexml_load_file(RESTAURANT_REVIEWS_PATH); //loading file into variable "restaurants"
extract ($_POST); //extracting all values from form
$confirm = false;
//testing output: var_dump($_POST);

//DISPLAY RESTAURANT NAMES:
//if nothing or if "selec one..." is selected from dropdown list
if (!isset($restaurantDropDown) || $restaurantDropDown == -1 )
{
    $restaurantDropDown = -1; //set dropdown list to "select one..."
    $address = ""; //clear address field
    $summary = ""; //clear summary field
    $rating = 1; //add 1 to rating
    $showDivFlag=false; //disable the showDivFlag div that contains the rest of the code    
}
else //if something is selected
{
    //creating a variable with the selected restaurant, using dropdown value as index
    $selectedRestaurant = $restaurants->restaurant[intval($restaurantDropDown)];
    
    //clicking on save changes button
    if (isset($saveChanges))
    {
        $selectedRestaurant->summary = $summary; //update summary
        $selectedRestaurant->rating = $rating; //update rating
        $restaurants->asXML(RESTAURANT_REVIEWS_PATH); //save xml  
        $confirm = "Revised restaurant review was saved to: restaurant_reviews.xml";
    }

    //displaying info for restaurant
    $addressLbl = true;
    $address = $selectedRestaurant->address->street . ", "
              .$selectedRestaurant->address->city . ", "
              .$selectedRestaurant->address->province . ", "
              .$selectedRestaurant->address->postalCode;    
    $summary = $selectedRestaurant->summary;
    $rating = intval($selectedRestaurant->rating);    
}
include 'Lab4Common/Header.php';
?>

<link rel="stylesheet" type="text/css" href="Site.css">  
<body margintop="60px" marginleft="80px">           
    <h1>Online Restaurant Review</h1><br><br>    
    <form method='post' action=Index.php >        
        <div class='form-group row'>
            <label for='restaurant' class='col-lg-2 col-form-label'><b>Restaurant:</b></label>
            <div class='col-lg-4'>  
            <select name=restaurantDropDown value='' class='form-control' id='restaurantDropDown' onchange="this.form.submit()">
                <option value=''>Select one...</option>
                <?php               
                $allRestaurants = $restaurants->restaurant; //loading all restaurants from file
                for ($i = 0 ; $i < count($restaurants->restaurant); $i++)
                {
                    $restIndex = $allRestaurants[$i]; //creating index number coming from for loop
                    print "<option value='$i' ".($restaurantDropDown == $i ? 'Selected' : '' )." >$restIndex->name</option>";
                    //printing option number = index. If selected dropdown = index, then mark as selected. Display selected index name
                }                
                ?>    
            </select></div>
        </div><br>  
        
        <!--creating a super div to be displayed or hidden:-->
        <div id="results" <?php if ($showDivFlag===false){?>style="display:none"<?php } ?>>      
            <div class='form-group row'>
                <label for='address' class='col-lg-2 col-form-label' id='addressLbl' name='addressLbl' ><b>Address: </b></label>
                <div class='col-lg-4'>
                    <textarea class='form-control' name="address" id='address' rows='2' cols='30' readonly ><?=$address?></textarea>              
                </div>
            </div><br>        
            <div class='form-group row'>
                <label for='summary' class='col-lg-2 col-form-label'><b>Summary: </b></label>
                <div class='col-lg-4'>           
                    <textarea class='form-control' name="summary" id='summary' rows='5' cols='30'><?=$summary?></textarea>
                </div>
            </div><br>    
            <div class='form-group row'>
                <label for='rating' class='col-lg-2 col-form-label'><b>Rating:</b></label>
                <div class='col-lg-4'>
                <select class='form-control' id='rating' name='rating'>
                <?php
                    for ($i=1; $i<=5; $i++)
                    {
                        //value = index from the loop; if rating from file = index, then mark as selected, if not leave blank. Display option number
                        print "<option value='$i' ". ($rating == $i ? 'Selected' : '')." > $i </option>";                    
                    }            
                ?>                            
                </select></div>
            </div>       
            <div class='form-group row'> 
                <label for='confirm' class='col-lg-8 col-form-label' style='color:greenyellow' id='confirm'><b><?php print $confirm ?> </b></label>            
            </div>     
            <div class='form-group row'>                
                <button type='saveChanges' name='saveChanges' class='btn btn-primary col-lg-2'>Save Changes</button>                
            </div>          
        </div>
    </form>   
</body>
    
<?php include 'Lab4Common/Footer.php';?>
