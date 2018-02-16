<?php
session_start();

include ("dbconnect.php");
header("Title:Procurement Portal");

$email_address = '';
if (isset($_SESSION['email_address'])) {
	$email_address = $_SESSION['email_address'];
} elseif (isset($_POST['email_address'])) {
	$_SESSION['email_address'] = $_POST['email_address'];
	$email_address = $_POST['email_address'];
}

if (isset($_POST['submit'])) {
	foreach($_POST as $key => $value) {
		$$key = mysqli_real_escape_string($mysqli, $value);
	}
	$query = "SELECT email FROM vendor WHERE email='$email_address'";
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($email);
		echo "<center>";
		$stmt->fetch();
			if ($email) {
				?>
				<h1>The E-Mail Address entered is already registered</h1>
				<h3>Please feel free to browse our current procurement opportunities</h3>
				<br><br>
				<?php
			} else {
				$appdate = date("Y-m-d");
		
				$sql = "INSERT INTO vendor (appdate,signature,visa_accepted,name,address,city,state,zipcode,contact,title,phone,fax,fein,email,website,payment_mail,mailing_address,mailing_city,mailing_state,mailing_zipcode,payment_terms,shipping_terms,business_type,business_structure,specialty,small_business,minority_business,minority_type,certified_business,certification_authority,fee,solemail) VALUES ('$appdate','$signature','$visa_accepted','$business_name','$address','$city','$state','$zipcode','$contact','$title','$phone','$fax','$fein','$email_address','$website','$payment_mail','$mailing_address','$mailing_city','$mailing_state','$mailing_zipcode','$payment_terms','$shipping_terms','$business_type','$business_structure','$specialty','$small_business','$minority_business','$minority_type','$certified_business','$certification_authority',1000.00,'$autoemail')";
				$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
				$_SESSION['email_address'] = $email_address;
				?>
				<h1>Thank You for your submission.</h1>
				You may now browse the available procurement opportunites by clicking the appropriate button below.<br>
				<s>Please note: The Vendor Annual Fee must be paid prior to submitting/acceptance of any bid packages by the vendor.</s><br>
				<s>You can pay this fee now, by clicking the 'Pay Annual Fee' button.</s><br>
				<s>You will be able to navigate to the opportunity page after the fee has been paid.</s>
				<center><h1>Your first year of access is FREE!</h1></center>
				<?php
			}
		$stmt->close();
	}	

	?>
		<form method='post' action='index.php'><br>
			<input type='submit' name='browse' value='Browse Opportunities'>
			<input type='submit' name='ask' value='Questions &amp; Answers'>
			<input type='submit' name='payfee' value='Pay Annual Fee'>
			<input type='submit' name='bid' value='Place Bid'><br>
		</form>
	</center>
	<?php
} else if (isset($_POST['browse'])) {
	?>
	<center>
		<h1>Browsing Current Opportunities</h1>
		<h2>You must be registered and validated to received the solicitations</h2>

		<form method='post' action='index.php'><br>
			<b>E-Mail Address to receive solicitation(s):</b> 
			<input type='text' name='email_address' value='<?php echo $email_address; ?>'><br><br>
			<input type='submit' name='browser' value='Submit'>
			<input type='submit' name='payfee' value='Validate Access'>
			<input type='submit' name='register' value='Register to Receive Solicitations'><br>
		</form>
	</center>
	<?php
} else if (isset($_POST['browser'])) {
	?>
	<center><h1>Current Opportunities</h1></center><br>
	<center><h2>You must be registered and validated to received these solicitations</h2></center><br>
	<?php
	$sql = "SELECT validated FROM vendor WHERE email='$email_address'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
	$row = $result->fetch_row();
	$validated = $row[0];

	$today = date("Y-m-d");

	$sql = "SELECT * FROM solicitations WHERE duedate >'$today'";
	$stmt2 = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
	?>
	<center>
	<form method='post' action='index.php'><br>
		<input type='hidden' name='email_address' value='<?php echo $email_address;?>'>
	<?php
	if ($stmt2->num_rows > 0) {
		$stmt2->bind_result($sol_num,$title,$duedate,$budget,$synopsis,$description,$filename,$onlinebid);
		while ($stmt2->fetch_row())
		{
			if ($validated == 'Y') {
				echo "<input type='checkbox' name='sol[]' value='$row[0]'>";
			}
			echo "<b>Sol #:</b> $row[0]  <b>Due:</b> $row[2] <b>Title:</b> $row[1]<br>";
			echo "<b>Budget:</b> $row[3]<br>";
			echo "<b>Synopsis:</b> $row[4]<br><br>";
		}	
	} else {
		echo "<i>No Solicitations are available</i><br/><br/>";
	}

	if ($validated == 'Y') {
		echo "<input type='Submit' name='email' value='Request Selected Solicitations'>";
	} else {
		echo "<input type='Submit' name='payfee' value='Validate'>";
	}
	echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>";
	echo "</form>";

	echo "<h5>If no check box preceeds the Solicitation number, you must be validated.</h5></center>";
} else if (isset($_POST['paybypaypal'])) {
	$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
	$row = $result->fetch_row();
	$fee = $row[0];

	echo "<center>";
	if ($fee) {
		?>
		<center><h2>Payment of Fee via Your American Express Card</h2></center>
		<center>Your credit card will be charged as follows:</center><br>
		<form method="post" action="https://www.paypal.com/cgi-bin/webscr" target="new">
			<input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="charset" value="utf-8" />
			<input type="hidden" name="business" value="<?php echo $_SETTINGS['PAYPAL_EMAIL']; ?>" />
			Description: <b><?php echo $_SETTINGS['COMPANY_NAME']; ?> Annual Procurement Fee by <?php echo $email_address; ?></b><br/>
			<input type="hidden" name="item_name" value="<?php echo $_SETTINGS['COMPANY_NAME']; ?> Annual Procurement Fee by <?php echo $email_address; ?>"><br>
			Amount: <b>$<?php echo $fee;?>+Tax</b><br/>
			<input type="hidden" name="amount" value="<?php echo $fee;?>">
			<input type="hidden" name="tax" value="1">
			<input type="hidden" name="currency_code" value="USD">
			<input type='submit' value="Click to Transfer to Secure Server">
		</form>
		<br/>
		<form method="post" action="index.php">
			<input type="submit" name="returntomain" value="Return to Main" />
		</form>
		<?php
	} else {
		?>
		<h1>E-Mail Address not found. Please register</h1>
		<form method='post' action='index.php'><br>
			<input type='Submit' name='register' value='Register to Receive Solicitations'><br>
		</form>
		<?php
	}
	echo "</center>";
} else if (isset($_POST['payfee'])) {
	header("Title:Annual Fee Payment");
	?>
	<center><h1>Pay Your Annual Fee</h1></center><br>
	<center><h2>Your annual fee is valid each year until your anniversary date<br>(the date when you initialliy registered).</h2></center><br>
	<center>
	<form method='post' action='index.php'><br>
		<b>E-Mail:</b> <input type='text' name='email_address' value='<?php echo $email_address; ?>'><br><br>
		<input type='submit' name='paybycheck' value='Pay by Check'>
		<input type='submit' name='paybypaypal' value='Pay by PayPal'>
		<input type='submit' name='register' value='Register to Receive Solicitations'>
		<input type='submit' name="returntomain" value='Return to Main'><br>
	</form>
	</center>
	<?php
} else if (isset($_POST['paybycheck'])) {
	?>
	<center>
		<h3>Pay by check</h3>
		<form method='post' action='index.php'><br>
			<b>E-Mail:</b> <input type='text' name='email_address' value='<?php echo $email_address;?>'><br><br>
			<b>RTN/ABA:</b> <input type='text' name='rtnaba' value=''>
			<b>Account Number:</b> <input type='text' name='acctno' value=''><br>
			<b>Bank Name &amp; Address:</b><br><textarea type='text' name='bankinfo' rows='5' cols='60' value=''></textarea><br>
			<b>Account Holder Name &amp; Address:</b><br><textarea type='text' name='holderinfo' rows='5' cols='60' value=''></textarea><br>
			<input type='Submit' name='paynow' value='Pay Now!'>
			<input type='Submit' name='register' value='Register to Receive Solicitations'><br>
		</form>
	</center>
	<?php
} else if (isset($_POST['paynow'])) {
	foreach($_POST as $key => $value) {
		$$key = mysqli_real_escape_string($mysqli, $value);
	}
	
	$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
	$row = $result->fetch_row();
	$fee = $row[0];

	echo "<center>";
	if ($fee) {
		echo "<h1>Please Confirm Your Payment</h1>";
		echo "An electronic check will be presented on the following account in the amount of <b>$fee</b> dollars:<br>";
		echo "RTN/ABA: $rtnaba<br>";
		echo "Account Number: $acctno<br>";
		echo "Bank Information: $bankinfo<br>";
		echo "Account Holder Information: $holderinfo<br>";
		echo "<form method='post' action='index.php'><br>";
		echo "<input type='hidden' name='email_address' value='$email_address'>";
		echo "<input type='hidden' name='rtnaba' value='$rtnaba'>";
		echo "<input type='hidden' name='acctno' value='$acctno'>";
		echo "<input type='hidden' name='bankinfo' value='$bankinfo'>";
		echo "<input type='hidden' name='holderinfo' value='$holderinfo'>";
		echo "<input type='Submit' name='confirmpayment' value='I am Legally eligible to Authorized Payment from this Account'><br>";
		echo "<input type='Submit' name='cancel' value='Cancel This Payment'><br>";
		echo "</form>";

	} else {
		echo "<h1>E-Mail Address not found. Please register</h1>";
		echo "<form method='post' action='index.php'><br>";
		echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>";
		echo "</form>";
	}
	echo "</center>";
} else if (isset($_POST['confirmpayment'])) {
	foreach($_POST as $key => $value) {
		$$key = mysqli_real_escape_string($mysqli, $value);
	}
	$sql = "INSERT INTO echeck (email,rtnaba,acctno,bankinfo,holderinfo) VALUES ('$email_address','$rtnaba', DES_ENCRYPT('$acctno'),'$bankinfo','$holderinfo')";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());

	?>
	<center>
		<h1>Thank You for Your Payment</h1>
		Once your e-check has been paid, your bids will be validated.
		You must wait for validation before submitting bids to the procurements.
		<br/><br/><br/>
		<form method="post" action="index.php">
			<input type="submit" name="browse" value="Browse Opportunities">
			<input type="submit" name="returntomain" value="Return to Main"><br>
		</form>
	</center>
	<?php
} else if (isset($_POST['email'])) {
	$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
	$row = $result->fetch_row();

	$today = date("Y-m-d");

	echo "<center>";
	if ($row[0]) {
		$sql = "SELECT * FROM solicitations WHERE duedate>'$today'";
		$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
				$num_of_rows = $result->num_rows;

				$i = 0;

		echo "<h1>The Results of Your Inquiry</h1>";
		while ($row = $result->fetch_row())
		{
			if ($row[$i]) {
				$message = "Solication #: $row[0]\nTitle: $row[1]\nDue Date: $row[2]\n\nSynopsis:\n$row[4]\n\nDescription:\n$row[5]";
				mail($email_address, "Re: SOLICITATION You Have Requested ($row[1])", $message,"From:Do Not Reply");
			}
			$i++;
		}

		echo "<h1>Thank You!</h1>";
		echo "Your Request has been send to: <b>$email_address</b>";
		echo "<br><br>";
		echo "<form method='post' action='index.php'><br>";
		echo "<input type='Submit' name='browse' value='Browse Opportunities'>";
		echo "<input type='Submit' name='ask' value='Questions & Answers'>";
		echo "<input type='Submit' name='bid' value='Place Bid'><br>";
		echo "</form>";
	} else {
		?>
		<h1>E-Mail Address not found. Please register</h1>
		<br/>
		<form method='post' action='index.php'>
			<input type='Submit' name='register' value='Register to Receive Solicitations'><br>
		</form>
		<?php
	}
	echo "</center>";
} else if (isset($_POST['register'])) {
	$appdate = date("Y-m-d");
	?>
	<center><h1>New Vendor Application</h1></center>
	Please complete as much information as necessary. A completed application will ensure prompt
	delivery of bid packages and payment processing.
	<br><br>
	<form method='post' action='index.php'><br>

	<table align="center" border = 1>
	<tr align=center>
	<td>Date of Application: <b><?php echo $appdate; ?></b></td>
	<td>Signature: (type name of person authorized to bind company)<input type='text' name='signature' value=''></td>
	</tr>
	<tr align=left>
	<td></td>
	<td>Visa Accepted: <select name='visa_accepted'>
	<option value='No'>No</option>
	<option value='Yes'>Yes</option>
	</select></td>
	</tr>
	</table><br>

	<table align="center" border = 1>
	<tr align=left>
	<td>Company Name</td><td><input type='text' name='business_name' value='' size=50></td>
	</tr>
	<tr align=left>
	<td>Address</td><td><input type='text' name='address' value='' size=60></td>
	</tr>
	<tr align=left>
	<td>City</td><td><input type='text' name='city' value='' size=30></td>
	<td>State</td><td><input type='text' name='state' value='' size=3></td>
	<td>Zip code</td><td><input type='text' name='zipcode' value='' size=10></td>
	</tr>";
	</table><br>

	<table align="center" border = 1>
	<tr align=left>
	<td>Contact</td><td><input type='text' name='contact' value='' size=30></td>
	<td>Title</td><td><input type='text' name='title' value='' size=20></td>
	<td>Phone</td><td><input type='text' name='phone' value='' size=11></td>
	<td>FAX</td><td><input type='text' name='fax' value='' size=11></td>
	</tr>
	</table><br>

	<table align="center" border = 1>
	<tr align=left>
	<td>F.E.I.D. or Social Security Number</td>
	<td><input type='text' name='fein' value='' size=10></td>
	</tr>
	<tr align=left>
	<td>E-Mail</td><td><input type='text' name='email_address' value='' size=50></td>
	</tr>
	<tr align=left>
	<td>Website</td><td><input type='text' name='website' value='' size=60></td>
	</tr>
	</table><br>

	<table align="center" border = 1>
	<tr align=left>
	<td>Invoice Payments to Mailed to</td><td><select name='payment_mail'>
	<option value='Above'>Above Address</option>
	<option value='Below'>As Shown Below</option>
	</select></td>
	</tr>
	<tr align=left>
	<td>Mailing Address</td><td><input type='text' name='mailing_address' value='' size=50></td>
	</tr>
	<tr align=left>
	<td>City</td><td><input type='text' name='mailing_city' value='' size=30></td>
	<td>State</td><td><input type='text' name='mailing_state' value='' size=3></td>
	<td>Zip code</td><td><input type='text' name='mailing_zipcode' value='' size=10></td>
	</tr>
	<tr align=left>
	<td>Payment terms</td><td><input type='text' name='payment_terms' value='' size=20></td>
	<td>Shipping terms</td><td><input type='text' name='shipping_terms' value='' size=20></td>
	</tr>
	</table><br>

	<table align="center" border = 1>
	<tr align=left>
	<td>Type of Business</td><td><select name='business_type'>
	<option value='NotSelected'>Please Select</option>
	<option value='Manufacturer'>Manufacturer/Producer</option>
	<option value='Construction'>Construction Services</option>
	<option value='Professional'>Professional Services</option>
	<option value='Store'>Distributor #1 (Store/Warehouse)</option>
	<option value='NoEstablishment'>Distributor #2 (No Establishment)</option>
	<option value='Printer'>Printing Company</option>
	<option value='ManuRep'>Manufacturer's Representative</option>
	<option value='Other'>Other</option>
	</select></td>
	<td>Business Structure</td><td><select name='business_structure'>
	<option value='NotSelected'>Please Select</option>
	<option value='Sole'>Sole Proprietorship</option>
	<option value='Partnership'>Partnership</option>
	<option value='Corporation'>Corporation (including LLC)</option>
	<option value='NonProfit'>Non-Profit</option>
	<option value='Individual'>Individual</option>
	</select></td>
	</tr>
	</table><br>

	<table align="center" border = 1>
	<tr align=left>
	<td>Specialty Major Services, Products, and/or Materials Offered</td>
	<td><textarea type='text' name='specialty' rows='5' cols='60' value=''></textarea></td>
	</tr>";
	</table><br>

	<table align="center" border = 1>
	<tr align=left>
	<td>Small Business Firm</td><td><select name='small_business'>
	<option value='No'>No</option>
	<option value='Yes'>Yes</option>
	</select></td>
	</tr>
	<tr align=left>
	<td>Minority Business Firm: (less than 100 F-T employess & $3 Million Net Worth)</td><td><select name='minority_business'>
	<option value='No'>No</option>
	<option value='Yes'>Yes</option>
	</select></td>
	</tr>
	<tr align=left>
	<td>Minorty Type</td><td><select name='minority_type'>
	<option value='NA'> </option>
	<option value='African American'>African American</option>
	<option value='American Woman'>American Woman</option>
	<option value='Hispanic American'>Hispanic American</option>
	<option value='Asian American'>Asian American</option>
	<option value='Native American'>Native American (Origin to an Indian Tribe)</option>
	</select></td>
	</tr>
	<tr align=left>
	<td>Certified Business</td><td><select name='certified_business'>
	<option value='No'>No</option>
	<option value='Yes'>Yes</option>
	</select></td>
	</tr>
	<tr align=left>
	<td>Certification Authority</td><td><input type='text' name='certification_authority' value=''></td>
	</tr>
	</table><br>

	<table align="center" border = 1>
	<tr align=left>
	<td>Receive E-Mail Notifications for new Solicitations</td>
	<td><select name='autoemail'>
	<option value='N'>No</option>
	<option value='Y'>Yes</option>
	</select></td>
	</tr>
	</table><br>

	<center>
	<input type='Submit' name='submit' value='Submit Application'>
	<input type='Submit' name='browse' value='Browse Opportunities'><br>
	</center>
	</form>
	<?php
} else if (isset($_POST['ask'])) {
	$today = date("Y-m-d");
	$sql = "SELECT number FROM solicitations WHERE duedate >'$today'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
	?>
	<center>
		<h1>Questions &amp; Answers</h1>
		<h2>Please select Solicitation # from list</h2>
		<br><br>
		<form method='post' action='index.php'><br>
		<b>Solicitation Number:</b>
		<select name='sol_num'>
		<option value='Not Selected'>-Please Select-</option>
	<?php
	while ($row = $result->fetch_row())
	{
		echo "<option value='$row[0]'>$row[0]</option>";
	}
	?>
		</select><br><br>
		<b>Registered E-Mail:</b> <input type='text' name='email_address' value='<?php echo $email_address; ?>' readonly><br><br>
		<input type='Submit' name='display_qa' value='Proceed'>
		<input type='Submit' name='register' value='Register to Receive Solicitations'>
		</form>
	</center>
	<?php
} else if (isset($_POST['bid'])) {
	$today = date("Y-m-d");
	$sql = "SELECT number FROM solicitations WHERE duedate>'$today'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
	?>
	<center>
		<h1>Place a Bid!</h1>
		<h2>Please select Solicitation # from list</h2>
		<br><br>
		<form method='post' action='index.php'><br>
		<b>Solicitation Number:</b><select name='sol_num'>
		<option value='Not Selected'>-Please Select-</option>
	<?php
	while ($row = $result->fetch_row())
	{
		echo "<option value='$row[0]'>$row[0]</option>";
	}
	?>
		</select><br><br>
		<b>Registered E-Mail:</b> <input type='text' name='email_address' value='<?php echo $email_address; ?>' readonly><br><br>
		<input type='Submit' name='placebid' value='Proceed'>
		<input type='Submit' name='register' value='Register to Receive Solicitations'>
		</form>
	</center>
	<?php
} else if(isset($_POST['placebid'])) {
	$sql = "SELECT validated FROM vendor WHERE email='$email_address'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
	$row = $result->fetch_row();

	$validated = $row[0];
	if ($validated != 'Y') {
		echo "<center>";
		echo "<h2>Unable to accept bid. Please validate your account!</h2>";
		echo "<form method='post' action='index.php'><br>";
		echo "<input type='Submit' name='payfee' value='Validate Access'>";
		echo "</form>";
		echo "</center>";
	} else {
		echo "<center>";
		echo "<h2>Please place your bid!</h2>";
		echo "<h5>Only one bid per registered user.</h5><br>";
		echo "<form method='post' action='index.php'><br>";
		echo "Solicitation No: $sol_num<br>";
		echo "<input type='hidden' name='solnum' value='$sol_num'>";
		echo "<input type='hidden' name='email_address' value='$email_address'>";
		echo "Bid Amount: <input type='text' name='bidamount' vale=''><br>";
		echo "Comments: ";
		echo "<textarea type='text' name='$bidcomments' rows='5' cols='60' value='$bidcomments'></textarea><br>";
		echo "<input type='Submit' name='bidexec' value='Submit Bid'>";
		echo "</form>";
		echo "</center>";
	}
} else if (isset($_POST['bidexec'])) {
	foreach($_POST as $key => $value) {
		$$key = mysqli_real_escape_string($mysqli, $value);
	}
	$sql = "SELECT * FROM bids WHERE email='$email_address' AND solnum='$solnum'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());

	if ($result->num_rows > 0) {
		$row = $result->fetch_row();
		if ($row[2] == $email_address) {
			?>
			<center><h2>You have already submitted a bid for this solicitation!</h2>
				<form method='post' action='index.php'><br>
				<input type='submit'  name='returntomain' value='Return to Main'><br>
				</form>
			</center>
			<?php
		} else {
			$sql = "INSERT INTO bids (solnum, email, amount, comments) VALUES ('$solnum','$email_address','$bidamount','$bidcomments')";
			$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
			?>
			<center><h2>Your bid submitted successfully for Solicitation $solnum!</h2>
				<form method='post' action='index.php'><br>
				<input type='submit' name='returntomain' value='Return to Main'><br>
				</form>
			</center>
			<?php
		}	
	} else {
	}
} else if (isset($_POST['display_qa'])) {
	$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());

	$row = $result->fetch_row();
	echo "<center>";
	echo "<h1>Questions & Answers</h1>";

	if ($row[0]) {
		$sol_num = $_POST['sol_num'];
		$sql = "SELECT * FROM questions WHERE solnum='$sol_num'";
		$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
		if ($result->num_rows > 0) {
			echo "<h2>Solicitation # $sol_num</h2>";

			while ($row = $result->fetch_row())
			{
				echo "<b>Question:</b><br>";
				echo "<blockquote>$row[1]</blockquote>";
				echo "<b>Answer:</b><br>";
				echo "<blockquote>$row[2]</blockquote><br>";
			}

			echo "<form method='post' action='index.php'><br>";
			echo "<input type='Submit' name='browse' value='Browse Opportunities'>";
			echo "<input type='Submit' name='post' value='Post Question'><br><br>";
			echo "<input type='hidden' name='email_address' value='$email_address'>";
			echo "<input type='hidden' name='sol_num' value='$sol_num'>";
			echo "<textarea type='text' name='question' rows='5' cols='60' value=''></textarea><br>";
			echo "</form>";
		} else {
			echo "<i>Please select a solicitation</i>";
			echo "<form method='post' action='index.php'><br>";
			echo "<input type='submit' name='returntomain' value='Return to Main'><br>";
			echo "</form>";
		}
	} else {
		echo "<h1>E-Mail Address not found. Please register</h1>";
		echo "<form method='post' action='index.php'><br>";
		echo "<input type='Submit' name='register' value='Register to Receive Solicitations'><br>";
		echo "</form>";
	}
	echo "</center>";
} else if(isset($_POST['post'])) {
	$sql = "INSERT INTO questions (solnum,question,email) VALUES ('$sol_num','$question','$email_address')";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());

	$sql = "SELECT fee FROM vendor WHERE email='$email_address'";
	$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
	$row = $result->fetch_row();
	$fee = $row[0];

	if ($fee) {
		$sql = "SELECT * FROM questions WHERE solnum='$sol_num'";
		$result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());

		?>
		<center>";
		<h1>Questions &amp; Answers</h1>
		<h2>Solicitation # <?php echo $sol_num; ?></h2>
		</center>
		<?php
		while ($row = $result->fetch_row())
		{
			?>
			<b>Question:</b><br>
			<blockquote><?php echo $row[2]; ?></blockquote>
			<b>Answer:</b><br>
			<blockquote><?php echo $row[3]; ?></blockquote><br>
			<?php
		}
		?>
		<form method='post' action='index.php'><br>
			<input type='Submit' name='browse' value='Browse Opportunities'>
			<input type='Submit' name='post' value='Post Question'><br><br>
			<input type='hidden' name='email_address' value='<?php echo $email;?>'>
			<input type='hidden' name='sol_num' value='<?php echo $sol_num;?>'>
			<textarea type='text' name='question' rows='5' cols='60' value=''></textarea><br>
		</form>
		<?php
	} else {
		?>
		<h1>E-Mail Address not found. Please register</h1>
		<form method='post' action='index.php'><br>
			<input type='Submit' name='register' value='Register to Receive Solicitations'><br>
		</form>
		<?php
	}
} else {
	?>
	<center>
		<h1>Welcome to <?php echo $_SETTINGS['COMPANY_NAME']; ?> Vendor Portal</h1>
		<h2>Use this portal to register, view and bid on current procurement opportunities.</h2>
		<br><br>
		<form method='post' action='index.php'><br>
			<input type='submit' name='register' value='Register to Receive Solicitations'>
			<input type='submit' name='browse' value='Browse Opportunities'>
			<input type='submit' name='ask' value='Questions &amp; Answers'>
			<input type='submit' name='bid' value='Place Bid'><br>
		</form>
	</center>
	<?php
}
?>