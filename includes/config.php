<?php

// use this file to configire the settings for your library

// Name of your Library - Not used for this program
define("institutionName", "Name of your Library");

// URL of your DB server - Not used for this program
define("dbServer", "db.server.url.ca");

// URL of your Encore server - Not used for this program
define("encoreServer", "encore.server.url.ca");

// URL of your app server
define("appServer", "app.server.url.ca");

// API Version - This app uses 4
define("apiVer", "4");

// Your API Key
define("apiKey", "API_KEY_FROM_III");

// Your API Secret
define("apiSecret", "YOUR_API_SECRET");

// Number of results you want to use.  Best to have it a large number so all expired patrons get emailed.
define("numberOfResults", "10000");

// For future development - No need to change at this time
define("resultOffset", "0");

// Email that the mail that is sent out will be from
define("mailFrom", "circ@library.ca");

// Subject of the email
define("mailSubject", "Welcome to the Milton Public Library");

// this defines the email that will be sent out.  The email will start with "Dear <Person's first name>"
define("emailBody", "<p>Welcome to the Milton Public Library.  We are glad you've chosen....</p>");



?>
