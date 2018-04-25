<?php
// Start the session. 
//Data gets changed when the user is switched, and gets erased when the user closes browser.
session_start();
?>

<!DOCTYPE html>
<html lang="en-US">
<html>
<head>
<title>Recitation Helper</title>
</head>
<body>

<h1>Welcome to Recitation Helper!</h1>
<p>Choose your recitation group</p>


<?php 

//Connect to database.  
$db_handle = pg_connect("host=localhost dbname=mydb user=felixkollin password=")
    or die("Can't connect to database".pg_last_error());  
  
//Get necessary data to create dropdown lists.
//Hardcoded to use course 1, instead of having one more dropdown list in beginning. For simplification purposes.
$students = pg_fetch_all(pg_query($db_handle,"select name,id from student"));
$recitations = pg_fetch_all(pg_query($db_handle,"select number,rid from recitation 
  inner join courserec on courserec.recitation = rid and course = 1"));
$recgroups = pg_fetch_all(pg_query($db_handle,"select letter from recgroup"));


?>
<!--Create dropdown lists-->

<!--form for the dropdown lists.-->
<form action="frontend.php" method="get">

<!--student list-->
<select name="student" id="student">
  <option selected="selected">Choose one</option>
  <?php
    foreach($students as $student) { ?>
      <option value="<?= $student['id'] ?>"><?= $student['name'] ?></option>
  <?php
    } ?>
</select> 

<!--recitation list-->
<select name="recitation" id="recitation">
  <option selected="selected">Choose one</option>
  <?php
    foreach($recitations as $recitation) { ?>
      <option value="<?= $recitation['rid'] ?>"><?= $recitation['number'] ?></option>
  <?php
    } ?>
</select>

<!--recgroup list-->
<select name="group" id="group">
  <option selected="selected">Choose one</option>
  <?php
    foreach($recgroups as $recgroup) { ?>
      <option value="<?= $recgroup['letter'] ?>"><?= $recgroup['letter']  ?></option>
  <?php
    } ?>
</select>

<!--Submit button-->
<input type="submit">
</form>

<!--Generate checkboxes-->
<?php

//Update page if GET is set with submitted dropdown list values. 
if($_GET){

//Save values in session.
$_SESSION['student'] = $_GET['student'];
$_SESSION['recitation'] = $_GET['recitation'];
$_SESSION['group'] = $_GET['group'];

//Select the problems related to recitation. 
$pquery = "select problem from hasproblem where recitation = $_GET[recitation]";
$problems = pg_fetch_all(pg_query($db_handle,$pquery));

?>

<!-- Form for checkboxes -->
<form action="frontend.php" method="post">

<?php
    //Checkbox generator. 
    foreach($problems as $problem) { 
      $pid = $problem['problem'];
      //Find problem number from pid
      $pnumber = pg_fetch_array(pg_query($db_handle, "select number from problem where pid = $pid"))['number'];
      //Find subproblem ids and letters.
      $spquery = "select spid, letter from subproblems inner join subproblem on spid = subproblems.subproblem and problem = $pid";
      $subproblems = pg_fetch_all(pg_query($db_handle,$spquery));
      foreach ($subproblems as $subproblem) { ?>
        <!-- Store spids in an array, and use problem number and subproblem letter for labels -->
      	<input type="checkbox" name="solved[]" value="<?= $subproblem['spid']?>"> <?= $pnumber ?>  <?= $subproblem['letter'] ?>     
     <?php } ?> 
<?php
  } 
  ?>

<!--Submit button-->
<input type="submit" name="submit" value="submit"> 
</form>

<?php
}
?>

<!--Send info to database-->
<?php

//Update page if POST is set with submitted subproblems
if (isset($_POST['submit'])) {
	$solved = $_POST['solved'];
	$student = $_SESSION['student'];
	$recitation = $_SESSION['recitation'];
	$group = $_SESSION['group'];

  //Clear past submissions.
  pg_query($db_handle, "delete from solved where student = $student and recitation=$recitation;");

  //Store submissions
	foreach ($solved as $s => $value) {
		$insertquery = "insert into solved values ($student, $recitation, '$group', $value, FALSE)";
		pg_query($db_handle, $insertquery);
	}

  //Query to calculate result from solved.
	$resultquery = "select student, recitation, sum(points) as res from
(select student, recitation, problem, count(subproblem) as numsub, avg(points) as points, avg(condition) as minsub from 
(solved natural join subproblems) as a 
inner join problem on a.problem = pid
group by student, recitation, problem
having count(subproblem) >= avg(condition)
) as a 
group by student, recitation having student = $student and recitation = $recitation;";

  //Save query result to $result. If no rows, set to 0. 
  $resultentry = pg_query($db_handle, $resultquery);
  if (pg_num_rows($resultentry) == 1) {
    $result = pg_fetch_result($resultentry, 0, 2);
  }
  else{
    $result = 0;
  }

  //Remove any trailing 0 decimals.
  $result = round($result);

  //Check if entry exists, then either update existing entry or insert into result table.
  if (pg_num_rows(pg_query($db_handle, "select * from result where student = $student and recitation = $recitation")) == 1) {
    pg_query($db_handle, "UPDATE result SET res = $result WHERE student = $student and recitation = $recitation;");
  }
  else{
    pg_query($db_handle, "Insert into result values($student, $recitation, $result)");
  }
	

  //Output user result.
	echo "Done! You got $result points!";
}

?>





</body>
</html>
