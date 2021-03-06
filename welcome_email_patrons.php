<?php
//include our functions file
include('includes/functions.php');

// change the code below this to customize the body of the email that is being sent to the patron

//date ranges to use in the json string
$lastMonday = date('Y-m-d', strtotime('-7 days',strtotime('previous monday')));
$lastSunday = date('Y-m-d', strtotime('-7 days',strtotime('this sunday')));
$today              = strtotime('today');
$today_string       = date('Y-m-d', strtotime('today'));
$yesterday          = date('Y-m-d', strtotime('-1 day', $today));
$firstDayOfNextMonth = date("Y-m-d", strtotime("first day of next month midnight"));
$lastDayOfNextMonth = date("Y-m-d", strtotime("last day of next month midnight"));

//json query built from Sierra Create Lists that gets a list of patrons
//who registered yesterday at the library
$query_string = '
{
  "target": {
    "record": {
      "type": "patron"
    },
    "id": 83
  },
  "expr": {
    "op": "between",
    "operands": [
      "' . $yesterday . '",
      "' . $today_string . '"
    ]
  }
}';


echo "Showing records for patrons who registered between " . $yesterday . " and " . $today_string;
echo "<br />";

//uri that is unique to your Sierra environment to do a patron query
  $uri = 'https://';
  $uri .= appServer;
  $uri .= ':443/iii/sierra-api/v';
  $uri .= apiVer;
  $uri .= '/patrons/query';
  $uri .= '?limit=' . numberOfResults; //use this to limit the # of results
  $uri .= '&offset=' . resultOffset;


//setup the API access token
setApiAccessToken();

//get the access token we just created
$apiToken = getCurrentApiAccessToken();

//build the headers that we are going to use to post our json to the api
$headers = array(
    "Authorization: Bearer " . $apiToken,
    "Content-Type:  application/json"
);

//use the headers, url, and json string to query the api
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $uri);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//get the result from our json api query
$result = curl_exec($ch);
$patronIdArray = json_decode($result, true);
$patronIdArray = $patronIdArray["entries"];

//echo out the results.  Use custom stripped function to get only the patronID
$count = 1;

//iterate through the array and get each patron
foreach ($patronIdArray as $thisId) {


    //echo $thisId['link'];  this is the full link to the patron
    //echo stripped($thisId['link']);   this is just the patronID

    //get the patron detail from the api
    $patron_detail = getPatron(stripped($thisId['link']));

    //parse out the data into variables
    $name =  $patron_detail['names'][0];

    //sometimes the email is not set - use null and set it if it exists
    //i'm doing this because i only want patrons with emails for my purposes
    $email = NULL;
    if(isset($patron_detail['emails'][0]))
    {
      $email = $patron_detail['emails'][0];
    }
    $barcode = $patron_detail['barcodes'][0];
    $expirationDate = $patron_detail['expirationDate'];

    //echo out the info to the screen
    echo $count . " ";
    echo "ID: " . $barcode . " Name: " . $name . " Email: " . $email;
    echo "<br />";
    $count = $count + 1;

    //You might have a middle initial in the names, this will parse
    //so we only get the first and last names

    // explod out the patron name so the last name and first name are separate
    // if the patron has a middle initial it will be in the first name at this point
    $patron_names = explode(',', $name);

    // set the $last_name variable to be the last name (first item in the exploded array)
    $last_name = $patron_names[0];

    //set the rest of the exploded array to a different variable so we can parse it
    $first_name_init = $patron_names[1];

    //explode this so we can isolate the first name from a middle initial if there is one
    $first_name_and_initial = explode(" ", $first_name_init);

    //set the $first_name variable from the exploded array
    $first_name = $first_name_and_initial[1];



    //create the email to send to the patron
    $email_headers  = 'MIME-Version: 1.0' . "\r\n";
    $email_headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $email_headers .= 'From: ' . mailFrom . "\r\n";

    $emailBody = "Dear " . $first_name . ",";
    $emailBody .= emailBody;

    //send email - if you want to test this out and not actually email patrons
    //replace $email with your own email address in ''s   ie.  'chris.jasztrab@mpl.on.ca'

    mail($email,mailSubject,$emailBody,$email_headers);

}


?>
