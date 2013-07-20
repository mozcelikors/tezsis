<!-- 
/************************
 * TezSis: TezYonetimSistemi
 *
 * @brief Akademisyenler ve ögrenciler arasindaki arayüzü saglayarak tez islemlerini yürütmeyi saglayan PHP tabanli bir web sistemidir.
 * @version 1.1
 * @author Mustafa Özçelikörs <mozcelikors>
 * @contact mozcelikors@gmail.com
 * @website thewebblog.net
 ******/
-->

<?php require_once('Connections/connect.php'); ?><?php require_once('modules/safety.php'); ?><?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedTeachers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strTeachers, $strGroups, $TeacherName, $TeacherGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Teachername set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($TeacherName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrTeachers = Explode(",", $strTeachers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($TeacherName, $arrTeachers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($TeacherGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strTeachers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "teacher_login.php";
if (!((isset($_SESSION['MM_Teachername'])) && (isAuthorized("",$MM_authorizedTeachers, $_SESSION['MM_Teachername'], $_SESSION['MM_TeacherGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
function SAFE_GET($theVar){
	$returnVal = addslashes(strip_tags($_GET[$theVar]));
  	$returnVal = preg_replace("/\<\?php.+\?\>/isUe", "", $returnVal);
  	$returnVal = preg_replace("/\<\?.+\?\>/isUe", "", $returnVal);
  	$returnVal = preg_replace("/<script[^>]*>.*?< *script[^>]*>/i", "", $returnVal);
  	$returnVal = preg_replace("/<script[^>]*>.*<*script[^>]*>/i", "", $returnVal);
  	$returnVal = preg_replace("/<script[^>]*>/i", "", $returnVal);
  	$returnVal = preg_replace("/<style[^>]*>.*<*style[^>]*>/i", "", $returnVal);
  	$returnVal = preg_replace("/<style[^>]*>/i", "", $returnVal);
	return $returnVal;
}
?>
<?php
$colname_userdetails = "-1";
if (SAFE_GET('userID')) {
  $colname_userdetails = (get_magic_quotes_gpc()) ? SAFE_GET('userID') : addslashes(SAFE_GET('userID'));
}
mysql_select_db($database_connect, $connect);
$query_userdetails = sprintf("SELECT * FROM users WHERE userID = %s", $colname_userdetails);
$userdetails = mysql_query($query_userdetails, $connect) or die(mysql_error());
$row_userdetails = mysql_fetch_assoc($userdetails);
$totalRows_userdetails = mysql_num_rows($userdetails);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>&Ouml;&#287;renci G&ouml;r&uuml;nt&uuml;leme</title>
<style type="text/css">
<!--
.style2 {font-size: 14px}
#userDisplay{
	width:400px;
	height:400px;
	float:left;
	padding:10px;
	background-color:#DFDFDF;
	margin-bottom:15px;
	border:1px solid #CCCCCC;
	font-size:11px;
	font-family:'Tahoma';

}
-->
</style>
</head>

<body>
<div id="userDisplay">
<h1>&Ouml;&#287;renci Detay&#305; </h1>
<span class="style2"><strong><br />
&Ouml;&#287;renci Numaras&#305;:</strong> <?php echo $row_userdetails['userSchoolno']; ?>
<br />
<br />
<strong>&Ouml;&#287;renci &#304;smi:</strong> <?php echo $row_userdetails['userFullname']; ?>
<br />
<br />
<strong>&Ouml;&#287;renci Email:</strong> <?php echo $row_userdetails['userEmail']; ?>
<br />
<br />
<strong>&Ouml;&#287;renci Bilgileri:</strong> <?php echo $row_userdetails['userAbout']; ?></span>
</div>
</body>
</html>
<?php
mysql_free_result($userdetails);
?>
