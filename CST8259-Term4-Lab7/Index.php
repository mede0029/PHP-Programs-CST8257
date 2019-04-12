<?php
//THIS FILE CONTAINS THE PHP FORM + 2 FUNCTIONS USED TO DISPLAY AND UPDATE DATA, COMING FROM LOGIC FILE
session_start();
define ("RESTAURANT_REVIEWS_PATH", "restaurant_reviews.xml"); //creating a path for the file address
$restaurants = simplexml_load_file(RESTAURANT_REVIEWS_PATH); //loading file into variable "restaurants"
include 'Lab4Common/Header.php';
$confirmation = "";
?>

<!--Form-->
<link rel="stylesheet" type="text/css" href="Site.css">  
<body margintop="60px" marginleft="80px">           
    <h1>Online Restaurant Review</h1><br><br>    
       
        <div class='form-group row'>
            <label for='restaurant' class='col-lg-2 col-form-label'><b>Restaurant:</b></label>
            <div class='col-lg-4'>  
            <select name=restaurantDropDown value='' class='form-control' id='restaurantDropDown' onchange='getResData();'>
                <option value='Selected'>Select one...</option>
                <?php               
                $allRestaurants = $restaurants->restaurant; //loading all restaurants from file
                for ($i = 0 ; $i < count($restaurants->restaurant); $i++)
                {
                    $restIndex = $allRestaurants[$i]; //creating index number coming from for loop
                    print "<option value='$i' >$restIndex->name</option>";
                    //printing option number = index. If selected dropdown = index, then mark as selected. Display selected index name
                }                
                ?>    
            </select></div>
        </div><br>  
        
        <!--creating a super div to be displayed or hidden:-->
        <div id="results" style="display:none">       
            <div class='form-group row'>
                <label for='address' class='col-lg-2 col-form-label' id='addressLbl' name='addressLbl' ><b>Address: </b></label>
                <div class='col-lg-4'>
                    <textarea class='form-control' name="address" id='address' rows='2' cols='30' readonly ></textarea>              
                </div>
            </div><br>        
            <div class='form-group row'>
                <label for='summary' class='col-lg-2 col-form-label'><b>Summary: </b></label>
                <div class='col-lg-4'>           
                    <textarea class='form-control' name="summary" id='summary' rows='5' cols='30'></textarea>
                </div>
            </div><br>    
            <div class='form-group row'>
                <label for='rating' class='col-lg-2 col-form-label'><b>Rating:</b></label>
                <div class='col-lg-4'>
                <select class='form-control' id='rating' name='rating'>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select></div>
            </div>       
            <div class='form-group row'> 
                <label for='confirmation' class='col-lg-8 col-form-label' style='color:greenyellow' id='confirmation'><b></b></label>            
            </div>     
            <div class='form-group row'>                
                <button type='saveChanges' name='saveChanges' class='btn btn-primary col-lg-2' onclick='saveChanges()'>Save Changes</button>                
            </div>          
        </div>      
<?php include 'Lab4Common/Footer.php';?>

<script>    
    //Ajax request to server / returning a JSON string
    function getResData() {        
        const e = document.getElementById("restaurantDropDown");//get the selected element from dropdown list
        const selectedRestaurantId = e.options[e.selectedIndex].value;//assign the index value to selectedRestaurantId

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "/Term4_Lab7/Logic.php?restaurantDropDown=" + selectedRestaurantId, true); //opening Logic file
        xhr.onload = function (e) {
            if (xhr.readyState === 4) { //error check
                if (xhr.status === 200) {  //error check
                    
                    //on change, display additional fields from div = "results"
                    var x = document.getElementById("results");
                    x.style.display = "block";   
                    
                    // creating new JSON element
                    const responseText = xhr.responseText; //responseText = all info for selected restaurant
                    const restaurant = JSON.parse(responseText); //parsing text to JSON

                    // Get the elements from the form
                    var textBoxAddress = document.getElementById("address"); //creating new variable from tag id=address (form)
                    var textBoxRating = document.getElementById("rating"); //creating new variable from tag id= rating (form)
                    var textBoxReview = document.getElementById("summary"); //creating new variable from id=summary (form)

                    // Adding new JSON element values to variables created from tag id's
                    textBoxAddress.value = restaurant.address;
                    textBoxRating.value = restaurant.rating;
                    textBoxReview.value = restaurant.summary;             
                } else {
                    console.error(xhr.statusText);
                }
            }
        };
            xhr.send(null);
        }
        
        //Ajax request to server / sending a JSON string
        function saveChanges() {
            const e = document.getElementById("restaurantDropDown"); //get the selected element from dropdown list
            const selectedRestaurantId = e.options[e.selectedIndex].value; //assign the index value to selectedRestaurantId

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "/Term4_Lab7/Logic.php?restaurantDropDown=" + selectedRestaurantId, true);//opening Logic file
            xhr.onreadystatechange = function() { console.log(xhr.responseText) } //updating part of the page without reloading it
            
            // Create the send body
            var textBoxRating = document.getElementById("rating"); //getting address from tag id=address (form)
            var textBoxReview = document.getElementById("summary"); //getting address from tag id=address (form)
            xhr.send(JSON.stringify({
                //updating the element
                rating: textBoxRating.value,
                summary: textBoxReview.value,
                saveChanges: true
            }));
            console.log(JSON.stringify({
                rating: textBoxRating.value,
                summary: textBoxReview.value,
                saveChanges: true
            }));
            
            //displaying confirmation label:
            var confirmation = document.getElementById("confirmation"); //retrieve the confirmation id element
            var text = document.createTextNode("Revised restaurant review was saved to: restaurant_reviews.xml");//creating a text node
            confirmation.removeChild(confirmation.childNodes[0]); //cleaning existing text
            confirmation.appendChild(text); //adding the text node to the element
        }
</script> 
</body>