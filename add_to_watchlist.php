<?php
// @author : Mustafa Özçelikörs
// @webSite : www.thewebblog.net
// @contact : mozcelikors@gmail.com

ob_start();
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "user_default.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
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
function SAFE_SESSION($theVar){
	$returnVal = addslashes(strip_tags($_SESSION[$theVar]));
  	$returnVal = preg_replace("/\<\?php.+\?\>/isUe", "", $returnVal);
  	$returnVal = preg_replace("/\<\?.+\?\>/isUe", "", $returnVal);
  	$returnVal = preg_replace("/<script[^>]*>.*?< *script[^>]*>/i", "", $returnVal);
  	$returnVal = preg_replace("/<script[^>]*>.*<*script[^>]*>/i", "", $returnVal);
  	$returnVal = preg_replace("/<script[^>]*>/i", "", $returnVal);
  	$returnVal = preg_replace("/<style[^>]*>.*<*style[^>]*>/i", "", $returnVal);
  	$returnVal = preg_replace("/<style[^>]*>/i", "", $returnVal);
	return $returnVal;
}
require_once('Connections/connect.php');

mysql_select_db($database_connect,$connect);

// Kullaniciyi seçelim, Resultseti loggeduser
$colname_loggeduser = SAFE_SESSION('MM_Username');
mysql_select_db($database_connect, $connect);
$query_loggeduser = sprintf("SELECT * FROM users WHERE userSchoolno = '%s'", $colname_loggeduser);
$loggeduser = mysql_query($query_loggeduser, $connect) or die(mysql_error());
$row_loggeduser = mysql_fetch_assoc($loggeduser);
$totalRows_loggeduser = mysql_num_rows($loggeduser);

// Istenen tezi seçelim
$requestID = SAFE_GET('thesisID');
mysql_select_db($database_connect, $connect);
$query_thesis = sprintf("SELECT * FROM thesis WHERE thesisID = '%d'", $requestID);
$thesis = mysql_query($query_thesis, $connect) or die(mysql_error());
$row_thesis = mysql_fetch_assoc($thesis);
$totalRows_thesis = mysql_num_rows($thesis);


// Bu kullanicinin onaylanmis tezi varmi check edelim.
$arequestStudentID = $row_loggeduser['userID'];
mysql_select_db($database_connect, $connect);
$query_checkARequest = sprintf("SELECT * FROM accepted_requests WHERE arequestStudentID = '%d'", $arequestStudentID);
$checkARequest = mysql_query($query_checkARequest, $connect) or die(mysql_error());
$row_checkARequest = mysql_fetch_assoc($checkARequest);
$totalRows_checkARequest = mysql_num_rows($checkARequest);


// Bu kullanici önceden bu teze istek göndermis mi check edelim
$requestStudentID = $row_loggeduser['userID'];
mysql_select_db($database_connect, $connect);
$query_checkRequest = sprintf("SELECT * FROM requests WHERE requestStudentID = '%d' AND requestThesisID='%d'", $requestStudentID, $requestID);
$checkRequest = mysql_query($query_checkRequest, $connect) or die(mysql_error());
$row_checkRequest = mysql_fetch_assoc($checkRequest);
$totalRows_checkRequest = mysql_num_rows($checkRequest);



// Kayitlarin bos olmamasi lazim
$studentID = $row_loggeduser['userID'];
$thesisID = $row_thesis['thesisID'] ;
$thesisTeacherID = $row_thesis['thesisTeacherID'] ;
if( (mysql_num_rows($thesis)>0) && (mysql_num_rows($loggeduser)>0) && ($row_thesis['thesisStatus']=="Uygun" && ($totalRows_checkRequest<=0))){
	// Uygun ise, veri girisi
	if( !($totalRows_checkARequest==1) ){
		$query_insertThis = sprintf("INSERT INTO `requests` (
									`requestID` ,
									`requestThesisID` ,
									`requestRelatedTeacherID` ,
									`requestStudentID`
									) VALUES (
									NULL , '%d', '%d', '%d'
									);
", $thesisID, $thesisTeacherID, $studentID);
		$insertThis = mysql_query($query_insertThis, $connect);
		if ($insertThis){
			$color = 'green';
			$message = "&#304;ste&#287;iniz ba&#351;ar&#305;l&#305; olarak g&ouml;nderildi. ";
		}else {
			$color = 'red';
			$message = "Isteginiz gonderilemedi. Lutfen kimlik bilgilerinizi dogru girip girmediginizi kontrol ediniz.";
		}
	// Uygun degil ise, hata sayfasi	
	}else{
		$color = 'red';
		$message = "Kabul edilmis bir bitirme konunuz zaten var. Bu islemi gerceklestiremezsiniz.";
	}
}else{
	if($totalRows_checkRequest>0){
		$color = 'red';
		$message = "Bir tez konusu icin iki kere istekte bulunamazsiniz.";
	}else{
		$color = 'red';
		$message = "Uygun olmayan bir islem yapmaya calisiyorsunuz. Isleminiz gerceklestirilemedi.";
	}
}




mysql_close($connect);
echo "<div style=\"text-align:center; margin:100px auto; font-family:'Arial'; font-size:14px; font-weight:bold;\">";
echo "<font color=\"".$color."\">";
echo $message.'<br /><br />';
echo "</font>";
echo "L&uuml;tfen bekleyiniz, y&ouml;nlendiriliyorsunuz.. ";
echo "</div>";
header( "refresh:2; url=user_default.php" );
ob_end_flush();
?>
<title>Send Thesis Request</title>