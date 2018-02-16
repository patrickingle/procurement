<?php
#
# Table structure for table 'vendor'
#
/*

CREATE TABLE vendor (
   seq tinyint(4) NOT NULL auto_increment,
   appdate date DEFAULT '0000-00-00' NOT NULL,
   signature varchar(30) NOT NULL,
   visa_accepted char(1) NOT NULL,
   name varchar(50) NOT NULL,
   address varchar(60) NOT NULL,
   city varchar(30) NOT NULL,
   state char(2) NOT NULL,
   zipcode varchar(9) NOT NULL,
   contact varchar(30) NOT NULL,
   title varchar(20) NOT NULL,
   phone varchar(10) NOT NULL,
   fax varchar(10) NOT NULL,
   fein varchar(9) NOT NULL,
   email varchar(50) NOT NULL,
   website varchar(60) NOT NULL,
   payment_mail char(1) NOT NULL,
   mailing_address varchar(50) NOT NULL,
   mailing_city varchar(30) NOT NULL,
   mailing_state char(2) NOT NULL,
   mailing_zipcode varchar(9) NOT NULL,
   payment_terms varchar(20) NOT NULL,
   shipping_terms varchar(20) NOT NULL,
   business_type varchar(30) NOT NULL,
   business_structure varchar(30) NOT NULL,
   specialty longtext NOT NULL,
   small_business char(1) NOT NULL,
   minority_business char(1) NOT NULL,
   minority_type varchar(30) NOT NULL,
   certified_business char(1) NOT NULL,
   certification_authority varchar(30) NOT NULL,
   fee decimal(6,2) DEFAULT '0.00' NOT NULL,
   solemail char(1) DEFAULT 'N' NOT NULL,
   validated char(1) DEFAULT 'N' NOT NULL,
   PRIMARY KEY (seq)
);

#
# Table structure for table 'echeck'
#

CREATE TABLE echeck (
   email varchar(30) NOT NULL,
   rtnaba varchar(9) NOT NULL,
   acctno varchar(20) NOT NULL,
   bankinfo longtext NOT NULL,
   holderinfo longtext NOT NULL
);

#

# Table structure for table 'solicitations'
#

CREATE TABLE solicitations (
   number varchar(13) NOT NULL,
   title varchar(80) NOT NULL,
   duedate date DEFAULT '0000-00-00' NOT NULL,
   budget decimal(15,2) DEFAULT '0.00' NOT NULL,
   synopsis longtext NOT NULL,
   description longtext NOT NULL,
   filename varchar(30) NOT NULL,
   onlinebid char(1) NOT NULL
);

#
# Table structure for table 'questions'
#

CREATE TABLE questions (
   seq tinyint(4) NOT NULL auto_increment,
   solnum varchar(13) NOT NULL,
   question longtext NOT NULL,
   answer longtext NOT NULL,
   email varchar(50) NOT NULL,
   PRIMARY KEY (seq)
);

#
# Table structure for table 'bids'
#

CREATE TABLE bids (
   seq tinyint(4) NOT NULL auto_increment,
   solnum varchar(13) DEFAULT '0' NOT NULL,
   email varchar(50) NOT NULL,
   amount decimal(10,0) DEFAULT '0' NOT NULL,
   comments longtext NOT NULL,
   bidtime timestamp(14),
   PRIMARY KEY (seq)
);

*/
	include ("dbconnect.php");
    header("Title:Procurement Portal");

    if ($submit) {
		$sql = "SELECT email FROM vendor WHERE email='$email_address'";
  		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);

		echo "<center>";
		if ($row[0]) {
			echo "<h1>The E-Mail Address entered is already registered</h1>\n";
			echo "<h3>Please feel free to browse our current procurement opportunities</h3>\n";
			echo "<br><br>\n";
		} else {
	    	$appdate = date("Y-m-d");

	    	$sql = "INSERT INTO vendor (appdate,signature,visa_accepted,name,address,city,state,zipcode,contact,title,phone,fax,fein,email,website,payment_mail,mailing_address,mailing_city,mailing_state,mailing_zipcode,payment_terms,shipping_terms,business_type,business_structure,specialty,small_business,minority_business,minority_type,certified_business,certification_authority,fee,solemail) VALUES ('$appdate','$signature','$visa_accepted','$business_name','$address','$city','$state','$zipcode','$contact','$title','$phone','$fax','$fein','$email_address','$website','$payment_mail','$mailing_address','$mailing_city','$mailing_state','$mailing_zipcode','$payment_terms','$shipping_terms','$business_type','$business_structure','$specialty','$small_business','$minority_business','$minority_type','$certified_business','$certification_authority',1000.00,'$autoemail')";
  			$result = mysql_query($sql) or die(mysql_error());

			echo "<h1>Thank You for your submission.</h1>\n";
			echo "You may now browse the available procurement opportunites by clicking the appropriate button below.<br>\n";
			echo "<s>Please note: The Vendor Annual Fee must be paid prior to submitting/acceptance of any bid packages by the vendor.</s><br>\n";
			echo "<s>You can pay this fee now, by clicking the 'Pay Annual Fee' button.</s><br>\n";
			echo "<s>You will be able to navigate to the opportunity page after the fee has been paid.</s>\n";
			echo "<center><h1>Your first year of access is FREE!</h1></center>\n";
		}

    	echo "<form method='post' action='$PHP_SELF'><br>\n";
		echo "<input type='Submit' name='browse' value='Browse Opportunities'>\n";
		echo "<input type='Submit' name='ask' value='Questions & Answers'>\n";
		echo "<input type='Submit' name='payfee' value='Pay Annual Fee'>\n";
		echo "<input type='Submit' name='bid' value='Place Bid'><br>\n";
		echo "</form>\n";
		echo "</center>";
	} else if ($browse) {
		echo "<center><h1>Browsing Current Opportunities</h1></center><br>\n";
		echo "<center><h2>You must be registered and validated to received the solicitations</h2></center><br>\n";

        echo "<form method='post' action='$PHP_SELF'><br>\n";
		echo "<b>E-Mail Address to receive solicitation(s):</b> <input type='text' name='email_address' value='$email_address'><br><br>\n";
		echo "<input type='Submit' name='browser' value='Submit'>\n";
		echo "<input type='Submit' name='payfee' value='Validate Access'>\n";
        echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>\n";

	} else if ($browser) {
		echo "<center><h1>Current Opportunities</h1></center><br>\n";
		echo "<center><h2>You must be registered and validated to received these solicitations</h2></center><br>\n";

		$sql = "SELECT validated FROM vendor WHERE email='$email_address'";
  		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);

		$validated = $row[0];

		$today = date("Y-m-d");

		$sql = "SELECT * FROM solicitations WHERE duedate>'$today'";
  		$result = mysql_query($sql) or die(mysql_error());

        echo "<form method='post' action='$PHP_SELF'><br>\n";
		echo "<input type='hidden' name='email_address' value='$email_address'>\n";

		while ($row = mysql_fetch_array($result))
		{
			if ($validated == 'Y') {
				echo "<input type='checkbox' name='sol[]' value='$row[5]'>\n";
			}
			echo "<b>Sol #:</b> $row[0]  <b>Due:</b> $row[2] <b>Title:</b> $row[1]<br>\n";
			echo "<b>Budget:</b> $row[3]<br>\n";
			echo "<b>Synopsis:</b> $row[4]<br><br>\n";
		}

		if ($validated == 'Y') {
			echo "<input type='Submit' name='email' value='Request Selected Solicitations'>\n";
		} else {
			echo "<input type='Submit' name='payfee' value='Validate'>\n";
		}
        echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>\n";
		echo "</form>\n";

		echo "<center><h5>If no check box preceeds the Solicitation number, you must be validated.</h5></center>\n";
    } else if ($paybyamex) {
		$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
  		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);

		echo "<center>\n";
		if ($row[0]) {
			echo "<center><h2>Payment of Fee via Your American Express Card</h2></center>\n";
			echo "<center>Your credit card will be charged as follows:</center><br>\n";
			echo "<form method='post' action='https://payflowlink.verisign.com/payflowlink.cfm' target='new'>\n";
			echo "<input type='hidden' name='LOGIN' value='vcrfix'>\n";
			echo "<input type='hidden' name='PARTNER' value='AmericanExpress'>\n";
			echo "Description: <b>Ingle Industries Corp. Annual Procurement Fee by $email_address</b>\n<input type='hidden' name='DESCRIPTION' value='Ingle Industries Corp. Annual Procurement Fee by $email_address'><br>\n";
    		echo "Amount: <b>$$row[0]</b>\n<input type='hidden' name='AMOUNT' value='$row[0]'><br>\n";
			echo "<input type='hidden' name='TYPE' value='S'><br>\n";
			echo "<input type='hidden' name='METHOD value='CC'><br>\n";
			echo "<input type='submit' value='Click to Transfer to Secure Server'>";
		} else {
			echo "<h1>E-Mail Address not found. Please register</h1>\n";
	        echo "<form method='post' action='$PHP_SELF'><br>\n";
	        echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>\n";
			echo "</form>\n";
		}
		echo "</center>\n";
	} else if ($payfee) {
	    header("Title:Annual Fee Payment");
		echo "<center><h1>Pay Your Annual Fee</h1></center><br>\n";
		echo "<center><h2>Your annual fee is valid each year until your anniversary date<br>(the date when you initialliy registered).</h2></center><br>\n";
		echo "<center>\n";
        echo "<form method='post' action='$PHP_SELF'><br>\n";
		echo "<b>E-Mail:</b> <input type='text' name='email_address' value='$email_address'><br><br>\n";
        echo "<input type='Submit' name='paybycheck' value='Pay by Check'>\n";
		echo "<input type='Submit' name='paybyamex' value='Pay by American Express'>\n";
        echo "<input type='Submit' name='register' value='Register to Receive Solicitations'>\n";
		echo "<input type='submit' value='Return to Main'><br>\n";
		echo "</form>\n";
		echo "</center>\n";
	} else if ($paybycheck) {
		echo "<h3>Pay by check</h3>\n";
        echo "<form method='post' action='$PHP_SELF'><br>\n";
		echo "<b>E-Mail:</b> <input type='text' name='email_address' value='$email_address'><br><br>\n";
		echo "<b>RTN/ABA:</b> <input type='text' name='rtnaba' value='$rtnaba'>\n";
		echo "<b>Account Number:</b> <input type='text' name='acctno' value='$acctno'><br>\n";
		echo "<b>Bank Name & Address:</b><br><textarea type='text' name='bankinfo' rows='5' cols='60' value='$bankinfo'></textarea><br>\n";
		echo "<b>Account Holder Name & Address:</b><br><textarea type='text' name='holderinfo' rows='5' cols='60' value='$holderinfo'></textarea><br>\n";
        echo "<input type='Submit' name='paynow' value='Pay Now!'>\n";
        echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>\n";
	} else if ($paynow) {
		$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
  		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);

		echo "<center>\n";
		if ($row[0]) {

			echo "<h1>Please Confirm Your Payment</h1>\n";
			echo "An electronic check will be presented on the following account in the amount of <b>$row[0]</b> dollars:<br>\n";
			echo "RTN/ABA: $rtnaba<br>\n";
			echo "Account Number: $acctno<br>\n";
			echo "Bank Information: $bankinfo<br>\n";
			echo "Account Holder Information: $holderinfo<br>\n";
	        echo "<form method='post' action='$PHP_SELF'><br>\n";
			echo "<input type='hidden' name='email_address' value='$email_address'>\n";
			echo "<input type='hidden' name='rtnaba' value='$rtnaba'>\n";
			echo "<input type='hidden' name='acctno' value='$acctno'>\n";
			echo "<input type='hidden' name='bankinfo' value='$bankinfo'>\n";
			echo "<input type='hidden' name='holderinfo' value='$holderinfo'>\n";
	        echo "<input type='Submit' name='confirmpayment' value='I am Legally eligible to Authorized Payment from this Account'><br>\n";
	        echo "<input type='Submit' name='cancel' value='Cancel This Payment'><br>\n";
			echo "</form>\n";

		} else {
			echo "<h1>E-Mail Address not found. Please register</h1>\n";
	        echo "<form method='post' action='$PHP_SELF'><br>\n";
	        echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>\n";
			echo "</form>\n";
		}
		echo "</center>\n";
	} else if ($confirmpayment) {
		$sql = "INSERT INTO echeck (email,rtnaba,acctno,bankinfo,holderinfo) VALUES ('$email_address','$rtnaba','$acctno','$bankinfo','$holderinfo')";
		$result = mysql_query($sql) or die(mysql_error());

		echo "<center>\n";
		echo "<h1>Thank You for Your Payment</h1>\n";
		echo "Once your e-check has been paid, your bids will be validated.\n";
		echo "You must wait for validation before submitting bids to the procurements.<br><br>\n";
        echo "<form method='post' action='$PHP_SELF'><br>\n";
		echo "<input type='Submit' name='browse' value='Browse Opportunities'>\n";
		echo "<input type='Submit' name='returntomain' value='Return to Main'><br>\n";
		echo "</form>\n";
		echo "</center>\n";
	} else if ($email) {
		$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
  		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);

		$today = date("Y-m-d");

        echo "<center>\n";
        if ($row[0]) {
 		    $sql = "SELECT * FROM solicitations WHERE duedate>'$today'";
  		    $result = mysql_query($sql) or die(mysql_error());
                    $num_of_rows = mysql_num_rows($result);

                    $i = 0;

            echo "<h1>The Results of Your Inquiry</h1>\n";
//			echo "<h3>Click on the document to view/download</h3>\n";

//       	            $message[] = "From Ingle Industries, Corp.\nThe following solicitations you have requested are available at these links.\n";
		    while ($row = mysql_fetch_array($result))
		    {
               if ($sol[$i]) {
                   	$message = "Solication #: $row[0]\nTitle: $row[1]\nDue Date: $row[2]\n\nSynopsis:\n$row[4]\n\nDescription:\n$row[5]\n";
					mail($email_address, "Re: SOLICITATION You Have Requested ($row[1])", $message,"From:Do Not Reply");
//                   	echo "$message\n";
               }
               $i++;
            }

    	    echo "<h1>Thank You!</h1>\n";
		    echo "Your Request has been send to: <b>$email_address</b>\n";
		    echo "<br><br>\n";
            echo "<form method='post' action='$PHP_SELF'><br>\n";
		    echo "<input type='Submit' name='browse' value='Browse Opportunities'>\n";
			echo "<input type='Submit' name='ask' value='Questions & Answers'>\n";
			echo "<input type='Submit' name='bid' value='Place Bid'><br>\n";
		    echo "</form>\n";
        } else {
		    echo "<h1>E-Mail Address not found. Please register</h1>\n";
    		    echo "<form method='post' action='$PHP_SELF'><br>\n";
    		    echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>\n";
		    echo "</form>\n";
        }
		echo "</center>";
	} else if ($register) {
        $appdate = date("Y-m-d");

        echo "<center><h1>New Vendor Application</h1></center>\n";
		echo "Please complete as much information as necessary. A completed application will ensure prompt\n";
		echo "delivery of bid packages and payment processing.\n";
		echo "<br><br>\n";
    	echo "<form method='post' action='$PHP_SELF'><br>\n";

		echo "<table border = 1>\n";
		echo "<tr align=center>\n";
		echo "<td>Date of Application: <b>$appdate</b></td>\n";
		echo "<td>Signature: (type name of person authorized to bind company)<input type='text' name='signature' value='$signature'></td>\n"; // limit of 30 characters
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td></td>\n";
    	echo "<td>Visa Accepted: <select name='visa_accepted'>\n";
		echo "<option value='No'>No</option>\n";
		echo "<option value='Yes'>Yes</option>\n";
		echo "</select></td>\n";
		echo "</tr>";
		echo "</table><br>\n";

		echo "<table border = 1>\n";
		echo "<tr align=left>\n";
		echo "<td>Company Name</td><td><input type='text' name='business_name' value='$business_name' size=50></td>\n"; // varchar(50) NOT NULL,
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>Address</td><td><input type='text' name='address' value='$address' size=60></td>\n"; // varchar(60) NOT NULL,
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>City</td><td><input type='text' name='city' value='$city' size=30></td>\n"; // varchar(30) NOT NULL,
		echo "<td>State</td><td><input type='text' name='state' value='$state' size=3></td>\n"; // char(2) NOT NULL,
		echo "<td>Zip code</td><td><input type='text' name='zipcode' value='$zipcode' size=10></td>\n"; // varchar(9) NOT NULL,
		echo "</tr>";
		echo "</table><br>\n";

		echo "<table border = 1>\n";
		echo "<tr align=left>\n";
		echo "<td>Contact</td><td><input type='text' name='contact' value='$contact' size=30></td>\n"; // varchar(30) NOT NULL,
		echo "<td>Title</td><td><input type='text' name='title' value='$title' size=20></td>\n"; // varchar(20) NOT NULL,
		echo "<td>Phone</td><td><input type='text' name='phone' value='$phone' size=11></td>\n"; // varchar(10) NOT NULL,
		echo "<td>FAX</td><td><input type='text' name='fax' value='$fax' size=11></td>\n"; // varchar(10) NOT NULL,
		echo "</tr>";
		echo "</table><br>\n";

		echo "<table border = 1>\n";
		echo "<tr align=left>\n";
		echo "<td>F.E.I.D. or Social Security Number</td>\n";
		echo "<td><input type='text' name='fein' value='$fein' size=10></td>\n"; // varchar(9) NOT NULL,
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>E-Mail</td><td><input type='text' name='email_address' value='$email_address' size=50></td>\n"; // varchar(50) NOT NULL,
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>Website</td><td><input type='text' name='website' value='$website' size=60></td>\n"; // varchar(60) NOT NULL,
		echo "</tr>";
		echo "</table><br>\n";

		echo "<table border = 1>\n";
		echo "<tr align=left>\n";
		echo "<td>Invoice Payments to Mailed to</td><td><select name='payment_mail'>\n";
		echo "<option value='Above'>Above Address</option>\n";
		echo "<option value='Below'>As Shown Below</option>\n";
		echo "</select></td>\n";
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>Mailing Address</td><td><input type='text' name='mailing_address' value='$mailing_address' size=50></td>\n"; // varchar(50) NOT NULL,
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>City</td><td><input type='text' name='mailing_city' value='$mailing_city' size=30></td>\n"; // varchar(30) NOT NULL,
		echo "<td>State</td><td><input type='text' name='mailing_state' value='$mailing_state' size=3></td>\n"; // char(2) NOT NULL,
		echo "<td>Zip code</td><td><input type='text' name='mailing_zipcode' value='$mailing_zipcode' size=10></td>\n"; // varchar(9) NOT NULL,
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>Payment terms</td><td><input type='text' name='payment_terms' value='$payment_terms' size=20></td>\n"; // varchar(20) NOT NULL,
		echo "<td>Shipping terms</td><td><input type='text' name='shipping_terms' value='$shipping_terms' size=20></td>\n"; // varchar(20) NOT NULL,
		echo "</tr>";
		echo "</table><br>\n";

		echo "<table border = 1>\n";
		echo "<tr align=left>\n";
		echo "<td>Type of Business</td><td><select name='business_type'>\n"; // varchar(30) NOT NULL,
		echo "<option value='NotSelected'>Please Select</option>\n";
		echo "<option value='Manufacturer'>Manufacturer/Producer</option>\n";
		echo "<option value='Construction'>Construction Services</option>\n";
		echo "<option value='Professional'>Professional Services</option>\n";
		echo "<option value='Store'>Distributor #1 (Store/Warehouse)</option>\n";
		echo "<option value='NoEstablishment'>Distributor #2 (No Establishment)</option>\n";
		echo "<option value='Printer'>Printing Company</option>\n";
		echo "<option value='ManuRep'>Manufacturer's Representative</option>\n";
		echo "<option value='Other'>Other</option>\n";
		echo "</select></td>\n";
		echo "<td>Business Structure</td><td><select name='business_structure'>\n"; // varchar(30) NOT NULL,
		echo "<option value='NotSelected'>Please Select</option>\n";
		echo "<option value='Sole'>Sole Proprietorship</option>\n";
		echo "<option value='Partnership'>Partnership</option>\n";
		echo "<option value='Corporation'>Corporation (including LLC)</option>\n";
		echo "<option value='NonProfit'>Non-Profit</option>\n";
		echo "<option value='Individual'>Individual</option>\n";
		echo "</select></td>\n";
		echo "</tr>";
		echo "</table><br>\n";

		echo "<table border = 1>\n";
		echo "<tr align=left>\n";
		echo "<td>Specialty Major Services, Products, and/or Materials Offered</td><td><textarea type='text' name='specialty' rows='5' cols='60' value='$specialty'></textarea></td>\n";
		echo "</tr>";
		echo "</table><br>\n";

		echo "<table border = 1>\n";
		echo "<tr align=left>\n";
		echo "<td>Small Business Firm</td><td><select name='small_business'>\n";
		echo "<option value='No'>No</option>\n";
		echo "<option value='Yes'>Yes</option>\n";
		echo "</select></td>\n";
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>Minority Business Firm: (less than 100 F-T employess & $3 Million Net Worth)</td><td><select name='minority_business'>\n";
		echo "<option value='No'>No</option>\n";
		echo "<option value='Yes'>Yes</option>\n";
		echo "</select></td>\n";
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>Minorty Type</td><td><select name='minority_type'>\n"; // varchar(30) NOT NULL,
		echo "<option value='NA'> </option>\n";
		echo "<option value='African American'>African American</option>\n";
		echo "<option value='American Woman'>American Woman</option>\n";
		echo "<option value='Hispanic American'>Hispanic American</option>\n";
		echo "<option value='Asian American'>Asian American</option>\n";
		echo "<option value='Native American'>Native American (Origin to an Indian Tribe)</option>\n";
		echo "</select></td>\n";
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>Certified Business</td><td><select name='certified_business'>\n";
		echo "<option value='No'>No</option>\n";
		echo "<option value='Yes'>Yes</option>\n";
		echo "</select></td>\n";
		echo "</tr>";
		echo "<tr align=left>\n";
		echo "<td>Certification Authority</td><td><input type='text' name='certification_authority' value='$certification_authority'></td>\n";
		echo "</tr>";
		echo "</table><br>\n";

		echo "<table border = 1>\n";
		echo "<tr align=left>\n";
		echo "<td>Receive E-Mail Notifications for new Solicitations</td>\n";
		echo "<td><select name='autoemail'>\n";
		echo "<option value='N'>No</option>\n";
		echo "<option value='Y'>Yes</option>\n";
		echo "</select></td>\n";
		echo "</tr>";
		echo "</table><br>\n";

    	echo "<input type='Submit' name='submit' value='Submit Application'>\n";
		echo "<input type='Submit' name='browse' value='Browse Opportunities'><br>\n";
    	echo "</form>\n";
	} else if ($ask) {
		$today = date("Y-m-d");
		$sql = "SELECT number FROM solicitations WHERE duedate>'$today'";
  		$result = mysql_query($sql) or die(mysql_error());

		echo "<center>\n";
		echo "<h1>Questions & Answers</h1>\n";
		echo "<h2>Please select Solicitation # from list</h2>\n";
		echo "<br><br>";
    	echo "<form method='post' action='$PHP_SELF'><br>\n";
		echo "<b>Solicitation Number:</b><select name='sol_num'>\n";
		echo "<option value='Not Selected'>-Please Select-</option>\n";

		while ($row = mysql_fetch_array($result))
		{
			echo "<option value='$row[0]'>$row[0]</option>\n";
		}

		echo "</select><br><br>\n";
		echo "<b>Registered E-Mail:</b> <input type='text' name='email_address' value='$email_address'><br><br>\n";
		echo "<input type='Submit' name='display_qa' value='Proceed'>\n";
    	echo "<input type='Submit' name='register' value='Register to Receive Solicitations'>\n";
    	echo "</form>\n";
	    echo "</center>\n";
	} else if ($bid) {
		$today = date("Y-m-d");
		$sql = "SELECT number FROM solicitations WHERE duedate>'$today'";
  		$result = mysql_query($sql) or die(mysql_error());

		echo "<center>\n";
		echo "<h1>Place a Bid!</h1>\n";
		echo "<h2>Please select Solicitation # from list</h2>\n";
		echo "<br><br>";
    	echo "<form method='post' action='$PHP_SELF'><br>\n";
		echo "<b>Solicitation Number:</b><select name='sol_num'>\n";
		echo "<option value='Not Selected'>-Please Select-</option>\n";

		while ($row = mysql_fetch_array($result))
		{
			echo "<option value='$row[0]'>$row[0]</option>\n";
		}

		echo "</select><br><br>\n";
		echo "<b>Registered E-Mail:</b> <input type='text' name='email_address' value='$email_address'><br><br>\n";
		echo "<input type='Submit' name='placebid' value='Proceed'>\n";
    	echo "<input type='Submit' name='register' value='Register to Receive Solicitations'>\n";
    	echo "</form>\n";
	    echo "</center>\n";
	} else if($placebid) {
		$sql = "SELECT validated FROM vendor WHERE email='$email_address'";
  		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);

		$validated = $row[0];
		if ($validated != 'Y') {
			echo "<center>\n";
			echo "<h2>Unable to accept bid. Please validate your account!</h2>\n";
	    	echo "<form method='post' action='$PHP_SELF'><br>\n";
			echo "<input type='Submit' name='payfee' value='Validate Access'>\n";
			echo "</form>\n";
			echo "</center>\n";
		} else {
			echo "<center>\n";
			echo "<h2>Please place your bid!</h2>\n";
			echo "<h5>Only one bid per registered user.</h5><br>\n";
	    	echo "<form method='post' action='$PHP_SELF'><br>\n";
			echo "Solicitation No: $sol_num<br>\n";
			echo "<input type='hidden' name='solnum' value='$sol_num'>\n";
			echo "<input type='hidden' name='email_address' value='$email_address'>\n";
			echo "Bid Amount: <input type='text' name='bidamount' vale=''><br>\n";
			echo "Comments: \n";
			echo "<textarea type='text' name='$bidcomments' rows='5' cols='60' value='$bidcomments'></textarea><br>";
			echo "<input type='Submit' name='bidexec' value='Submit Bid'>\n";
			echo "</form>\n";
			echo "</center>\n";
		}
	} else if ($bidexec) {
		$sql = "SELECT * FROM bids WHERE email='$email_address' AND solnum='$solnum'";
  		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);

		if ($row[2] == $email_address) {
			echo "<center><h2>You have already submitted a bid for this solicitation!</h2>\n";
	    	echo "<form method='post' action='$PHP_SELF'><br>\n";
			echo "<input type='submit' value='Return to Main'><br>\n";
			echo "</form>\n";
			echo "</center>\n";
		} else {
			$sql = "INSERT INTO bids (solnum, email, amount, comments) VALUES ('$solnum','$email_address','$bidamount','$bidcomments')";
	  		$result = mysql_query($sql) or die(mysql_error());
			echo "<center><h2>Your bid submitted successfully for Solicitation $solnum!</h2>\n";
	    	echo "<form method='post' action='$PHP_SELF'><br>\n";
			echo "<input type='submit' value='Return to Main'><br>\n";
			echo "</form>\n";
			echo "</center>\n";
		}
	} else if ($display_qa) {
		$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
  		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);

        if ($row[0]) {
			$sql = "SELECT * FROM questions WHERE solnum='$sol_num'";
  			$result = mysql_query($sql) or die(mysql_error());

			echo "<center>\n";
			echo "<h1>Questions & Answers</h1>\n";
			echo "<h2>Solicitation # $sol_num</h2>\n";
			echo "</center>";

			while ($row = mysql_fetch_array($result))
			{
				echo "<b>Question:</b><br>\n";
				echo "<blockquote>$row[1]</blockquote>\n";
				echo "<b>Answer:</b><br>\n";
				echo "<blockquote>$row[2]</blockquote><br>\n";
			}

	        echo "<form method='post' action='$PHP_SELF'><br>\n";
			echo "<input type='Submit' name='browse' value='Browse Opportunities'>\n";
	        echo "<input type='Submit' name='post' value='Post Question'><br><br>\n";
			echo "<input type='hidden' name='email_address' value='$email_address'>\n";
			echo "<input type='hidden' name='sol_num' value='$sol_num'>\n";
			echo "<textarea type='text' name='question' rows='5' cols='60' value='$question'></textarea><br>\n";
			echo "</form>\n";
		} else {
			echo "<center>\n";
			echo "<h1>E-Mail Address not found. Please register</h1>\n";
	        echo "<form method='post' action='$PHP_SELF'><br>\n";
	        echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>\n";
			echo "</form>\n";
			echo "</center>\n";
		}
	} else if($post) {
    	$sql = "INSERT INTO questions (solnum,question,email) VALUES ('$sol_num','$question','$email_address')";
		$result = mysql_query($sql) or die(mysql_error());

		$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
  		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);

        if ($row[0]) {
			$sql = "SELECT * FROM questions WHERE solnum='$sol_num'";
  			$result = mysql_query($sql) or die(mysql_error());

			echo "<center>\n";
			echo "<h1>Questions & Answers</h1>\n";
			echo "<h2>Solicitation # $sol_num</h2>\n";
			echo "</center>";

			while ($row = mysql_fetch_array($result))
			{
				echo "<b>Question:</b><br>\n";
				echo "<blockquote>$row[1]</blockquote>\n";
				echo "<b>Answer:</b><br>\n";
				echo "<blockquote>$row[2]</blockquote><br>\n";
			}

	        echo "<form method='post' action='$PHP_SELF'><br>\n";
			echo "<input type='Submit' name='browse' value='Browse Opportunities'>\n";
	        echo "<input type='Submit' name='post' value='Post Question'><br><br>\n";
			echo "<input type='hidden' name='email_address' value='$email_address'>\n";
			echo "<input type='hidden' name='sol_num' value='$sol_num'>\n";
			echo "<textarea type='text' name='question' rows='5' cols='60' value='$question'></textarea><br>\n";
			echo "</form>\n";
		} else {
			echo "<h1>E-Mail Address not found. Please register</h1>\n";
	        echo "<form method='post' action='$PHP_SELF'><br>\n";
	        echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>\n";
			echo "</form>\n";
		}
	} else {
	    echo "<center>\n";
		echo "<h1>Welcome to &lt;<b>Your Company</b>&gt; Vendor Portal</h1>\n";
		echo "<h2>Use this portal to register, view and bid on current procurement opportunities.</h2>\n";
		echo "<br><br>\n";
    	echo "<form method='post' action='$PHP_SELF'><br>\n";
    	echo "<input type='Submit' name='register' value='Register to Receive Solicitations'>\n";
		echo "<input type='Submit' name='browse' value='Browse Opportunities'>\n";
		echo "<input type='Submit' name='ask' value='Questions & Answers'>\n";
		echo "<input type='Submit' name='bid' value='Place Bid'><br>\n";
    	echo "</form>\n";
	    echo "</center>\n";
	}
?>