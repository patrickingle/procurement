<?php
session_start();
include 'dbconnect.php';
header("Title:Procurement Portal");

?>
<center>
    <h1>Welcome to <?php echo $_SETTINGS['COMPANY_NAME']; ?> Administrator Portal</h1>
    <br><br>
<?php

if (isset($_POST['login'])) {
    if ($_SETTINGS['ADMIN_USER'] === $_POST['username'] && $_SETTINGS['ADMIN_PASS'] === $_POST['userpass']) {
        $_SESSION['admin_user'] = $_POST['username'];
    } else {
        echo '<form method="post" action="admin.php">';
        echo '<b style="color:red;">Invalid login. Access denied</b><br/>';
        echo '<input type="submit" name="submit" value="Login" />';
        echo '</form>';
    }
} elseif (isset($_POST['addnewsol'])) {
	foreach($_POST as $key => $value) {
		$$key = mysqli_real_escape_string($mysqli, $value);
	}
    //echo '<pre>'; print_r(array($_POST,$_FILES)); echo '</pre>';
    $soldoc = $_FILES['soldoc']['name'];
    $sql = "INSERT INTO solicitations (number,title,duedate,budget,synopsis,description,filename,onlinebid) VALUES ('$solnum','$title','$duedate','$budget','$synopsis','$description','$soldoc','Y');";
    mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
    $uploaddir = dirname(__FILE__).'/uploads/';
    $uploadfile = $uploaddir . basename($_FILES['soldoc']['name']);
    
    if (move_uploaded_file($_FILES['soldoc']['tmp_name'], $uploadfile)) {
        echo "Soliciation #$solnum added successfully.<br/>";
    } else {
        echo "Possible file upload attack!<br/>";
    }
} elseif (isset($_POST['newsol'])) {
    $sql = "SELECT COUNT(seq) AS total FROM solicitations;";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
    $row = $result->fetch_row();
    $seq = $row[0] + 1;
    echo '<h2>New Solicitation</h2>';
    ?>
    <form enctype="multipart/form-data" method="post" action="admin.php">
        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
        <table align="center" border="1">
            <tr>
                <th>Solicitation Number</th>
                <td><input type="text" name="solnum" value="<?php echo date("YmdHi"); ?>.<?php echo $seq; ?>" readonly /></td>
            </tr>
            <tr>
                <th>Title</th>
                <td><input type="text" name="title" size="80" value="" required /></td>
            </tr>
            <tr>
                <th>Due Date</th>
                <td><input type="date" name="duedate" value="" required /></td>
            </tr>
            <tr>
                <th>Budget</th>
                <td><input type="number" name="budget" value="1.00" /></td>
            </tr>
            <tr>
                <th>Synopsis</th>
                <td><textarea rows="10" cols="60" name="synopsis" required ></textarea></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><textarea rows="10" cols="60" name="description" required ></textarea></td>
            </tr>
            <tr>
                <th>File Upload</th>
                <td><input type="file" name="soldoc" /></td>
            </tr>
            <tr>
                <th>Online Bidding</th>
                <td><input type="checkbox" name="onlinebid" /></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="addnewsol" value="Save" /></td>
            </tr>
        </table>
    </form>
    <?php
} elseif (isset($_POST['qna'])) {
    echo '<h2>Questions &amp; Answers Review(s)</h2>';
	$sql = "SELECT number FROM solicitations;";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
    ?>
    <h2>Please select Solicitation # from list</h2>
    <br><br>
    <form method='post' action='admin.php'><br>
    <b>Solicitation Number:</b><select name='sol_num'>
        <option value='Not Selected'>-Please Select-</option>
        <?php
        while ($row = $result->fetch_row())
        {
            echo "<option value='$row[0]'>$row[0]</option>";
        }
        ?>
    </select><br><br>
    <input type="submit" name="selectqna" value="View" />
    </form>
    <?php
} elseif (isset($_POST['selectqna'])) {
    $solnum = $_POST['sol_num'];
    echo '<h2>Questions &amp; Answers Review for Solicitation #'.$solnum.'</h2>';
    $sql = "SELECT * FROM questions WHERE solnum='$solnum';";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
    //echo '<pre>'; print_r($result); echo '</pre>';
    echo '<form method="post" action="admin.php">';
    echo '<input type="hidden" name="solnum" value="'.$solnum.'" />';
    echo '<table align="center" border="1">';
    echo '<th>Question</th><th>Answer</th>';
    while ($row = $result->fetch_row()) {
        echo '<tr><td>'.$row[2].'</td><td><textarea rows="6" cols="30" name="answers['.$row[0].']">'.$row[3].'</textarea></td></tr>';
    }
    echo '<tr><td colspan="2"><input type="submit" name="updateqna" value="Save" /></td></tr>';
    echo '</table>';
    echo '</form>';
} elseif (isset($_POST['updateqna'])) {
    $solnum = $_POST['solnum'];
    echo '<h2>Questions &amp; Answers Review for Solicitation #'.$solnum.'</h2>';
    //echo '<pre>'; print_r($_POST); echo '</pre>';
    if (isset($_POST['answers'])) {
        foreach ($_POST['answers'] as $seq => $answer) {
            //echo $seq . '->' . $answer .'<br/>';
            $sql = "UPDATE questions SET answer='$answer' WHERE seq=$seq;";
            mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
        }
        echo '<i><b style="color:green;">Answers saved successfully</b></i>';
    } else {
        echo '<i><b style="color:red;">No questions asked.</b></i>';
    }
} elseif (isset($_POST['bids'])) {
    echo '<h2>Bid Review(s)</h2>';
	$sql = "SELECT number FROM solicitations;";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
    ?>
    <h2>Please select Solicitation # from list</h2>
    <br><br>
    <form method='post' action='admin.php'><br>
    <b>Solicitation Number:</b><select name='sol_num'>
        <option value='Not Selected'>-Please Select-</option>
        <?php
        while ($row = $result->fetch_row())
        {
            echo "<option value='$row[0]'>$row[0]</option>";
        }
        ?>
    </select><br><br>
    <input type="submit" name="selectbid" value="View" />
    </form>
    <?php
} elseif (isset($_POST['selectbid'])) {
    $solnum = $_POST['sol_num'];
    echo '<h2>Bid Review for Solicitation #'.$solnum.'</h2>';
    $sql = "SELECT * FROM bids WHERE solnum='$solnum';";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_connect_error());
    //echo '<pre>'; print_r($result); echo '</pre>';
    echo '<form method="post" action="admin.php">';
    echo '<table align="center" border="1">';
    echo '<th>Vendor</th><th>Bid Amount</th><th>Vendor Document</th>';
    while ($row = $result->fetch_row()) {
        echo '<tr><td>'.$row[2].'</td><td>'.$row[3].'</td><td><a href="uploads/'.$row[6].'">'.$row[6].'</a></td></tr>';
    }
    echo '</table>';
    echo '</form>';
} elseif (isset($_POST['logout'])) {
    unset($_SESSION['admin_user']);
    echo '<form method="post" action="admin.php">';
    echo '<input type="submit" name="submit" value="Login" />';
    echo '</form>';
} else {
    if (isset($_SESSION['admin_user']) === false) {
        ?>
		<form method='post' action='admin.php'><br>
            <input type="text" name="username" value="" placeholder="Your user name" required /><br/><br/>
            <input type="password" name="userpass" value="" placeholder="Your password" required /><br/><br/>
			<input type='submit' name='login' value='Login'><br>
		</form>
    <?php
    }
}
if (isset($_SESSION['admin_user'])) {
    echo '<form method="post" action="admin.php">';
    echo '<input type="submit" name="newsol" value="New Soliciation" />';
    echo '<input type="submit" name="qna" value="Review Q&A" />';
    echo '<input type="submit" name="bids" value="Review Bids" />';
    echo '<input type="submit" name="logout" value="Logout" />';
    echo '</form>';
}
?>
</center>
