<!-- 
/************************
 * TezSis: TezYonetimSistemi
 *
 * @brief Akademisyenler ve öğrenciler arasındaki arayüzü sağlayarak tez işlemlerini yürütmeyi sağlayan PHP tabanlı bir web sistemidir.
 * @version 1.1
 * @author Mustafa Özçelikörs <mozcelikors>
 * @contact mozcelikors@gmail.com
 * @website thewebblog.net
 ******/
-->

<?php require_once('Connections/connect.php'); ?><?php require_once('modules/safety.php'); ?><?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Teachername'] = NULL;
  $_SESSION['MM_TeacherGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Teachername']);
  unset($_SESSION['MM_TeacherGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "teacher_login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Teachername set equal to their username. 
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

$MM_restrictGoTo = "error.php?err=accessDenied";
if (!((isset($_SESSION['MM_Teachername'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Teachername'], $_SESSION['MM_TeacherGroup'])))) {   
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
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE teachers SET teacherPassword=%s, teacherEmail=%s, teacherDivision=%s, teacherMultiple=%s, teacherNotes=%s WHERE teacherID=%s",
                       GetSQLValueString($_POST['teacherPassword'], "text"),
                       GetSQLValueString($_POST['teacherEmail'], "text"),
					   GetSQLValueString($_POST['teacherDivision'], "int"),
					   GetSQLValueString($_POST['teacherMultiple'], "text"),
                       GetSQLValueString($_POST['teacherNotes'], "text"),
                       GetSQLValueString($_POST['teacherID'], "int"));

  mysql_select_db($database_connect, $connect);
  $Result1 = mysql_query($updateSQL, $connect) or die(mysql_error());

  $updateGoTo = "teacher_edit_personal_info.php?update=success";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_connect, $connect);
$query_divisions = "SELECT * FROM divisions ORDER BY divisionID ASC";
$divisions = mysql_query($query_divisions, $connect) or die(mysql_error());
$row_divisions = mysql_fetch_assoc($divisions);
$totalRows_divisions = mysql_num_rows($divisions);

$colname_loggeduser = "-1";
if (isset($_SESSION['MM_Teachername'])) {
  $colname_loggeduser = (get_magic_quotes_gpc()) ? $_SESSION['MM_Teachername'] : addslashes($_SESSION['MM_Teachername']);
}
mysql_select_db($database_connect, $connect);
$query_loggeduser = sprintf("SELECT * FROM teachers WHERE teacherName = '%s'", $colname_loggeduser);
$loggeduser = mysql_query($query_loggeduser, $connect) or die(mysql_error());
$row_loggeduser = mysql_fetch_assoc($loggeduser);
$totalRows_loggeduser = mysql_num_rows($loggeduser);

?>
<?php 
mysql_select_db($database_connect, $connect);
$query_divisions2 = "SELECT * FROM divisions ORDER BY divisionID ASC";
$divisions2 = mysql_query($query_divisions2, $connect) or die(mysql_error());
$row_divisions2 = mysql_fetch_assoc($divisions2);
$totalRows_divisions2 = mysql_num_rows($divisions2);
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


?>
<!-- 
/************************
 * TezSis: TezYonetimSistemi
 *
 * @brief Akademisyenler ve öğrenciler arasındaki arayüzü sağlayarak tez işlemlerini yürütmeyi sağlayan PHP tabanlı bir web sistemidir.
 * @version 1.1
 * @author Mustafa Özçelikörs <mozcelikors>
 * @contact mozcelikors@gmail.com
 * @website thewebblog.net
 ******/
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="TezSis Tez Yönetim Sistemi" />
<meta name="webdeveloper" content="mozcelikors" />
<meta name="contact" content="mozcelikors@gmail.com" />
<meta name="keywords" content="TezSis" />
<meta name="title" content="TezSis Tez Yonetim Sistemi" />
<meta name="copyright"  content="Copyright © 2013 Mustafa Ozcelikors" />
<meta name="abstract" content="TezSis Tez Yonetim Sistemi" />

<title><?php include("info/info.inc"); ?></title>
<link href="css/style.css" type="text/css" rel="stylesheet" />
<?php if(eregi("Chrome",$_SERVER['HTTP_USER_AGENT'])){ ?>
<link href="css/chrome.css" type="text/css" rel="stylesheet" />
<?php }elseif(eregi("MSIE 9.0",$_SERVER['HTTP_USER_AGENT'])){ ?>
<link href="css/ie9.css" type="text/css" rel="stylesheet" />
<?php }elseif(eregi("Firefox",$_SERVER['HTTP_USER_AGENT'])){ ?>
<link href="css/firefox.css" type="text/css" rel="stylesheet" />
<?php }elseif(eregi("MSIE",$_SERVER['HTTP_USER_AGENT'])){ ?>
<link href="css/msie.css" type="text/css" rel="stylesheet" />
<?php } ?>
<script src="js/jquery-1.6.4.js" type="text/javascript" language="javascript"></script>
<script src="js/menu_animation.js" type="text/javascript" language="javascript"></script>
<script src="js/random_animation.js" type="text/javascript" language="javascript"></script>
<script src="js/formvalidations.js" type="text/javascript" language="javascript"></script>
<script src="js/formpasif.js" type="text/javascript" language="javascript"></script>
<script src="js/emailvalidations_teacher.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript" language="javascript">
function formValidate(){
		if(!document.form1.teacherPassword.value){
			alert("Bu alani bos birakamazsiniz. Guvenliginiz icin sayfa yenilenecektir.");
			window.location = "teacher_edit_personal_info.php";
			return false;
		}
		if(!document.form1.teacherEmail.value){
			alert("Bu alani bos birakamazsiniz. Guvenliginiz icin sayfa yenilenecektir.");
			window.location = "teacher_edit_personal_info.php";
			return false;
		}
		if(!document.form1.teacherNotes.value){
			alert("Bu alani bos birakamazsiniz. Guvenliginiz icin sayfa yenilenecektir.");
			window.location = "teacher_edit_personal_info.php";
			return false;
		}
		if( document.form1.teacherPassword.value  !=  document.form1.teacherPassword2.value ){
			alert("Girdiginiz sifreler uyusmuyor. Guvenliginiz icin sayfa yenilenecektir.");
			window.location = "teacher_edit_personal_info.php";
			return false;
		}
		return true;
	}
</script>

<style type="text/css">
<!--
.style2 {font-size: 9px}
#thesisCriteria {
	color:#CC6600;
	font-size:11px;
}
.style3 {
	color: #666666;
	font-weight: bold;
}
.style4 {
	color: #333333;
	font-weight: bold;
}
.style23 {font-size: 12px; color: #333333; font-weight: bold; }
-->
</style>
</head>

<body>
<div id="minWidth">

<div id="popUpMenu">
	<a class="buttonStyle buttonStylePopUp" href="teacher_edit_personal_info.php">Kişisel Bilgileri Düzenle</a>
	<a class="buttonStyle buttonStylePopUp" href="teacher_request_queue.php">Bekleyen İstekler</a>
	<a class="buttonStyle buttonStylePopUp" href="teacher_add_new_topic.php">Yeni Konu Ekle</a>
	<a class="buttonStyle buttonStylePopUp" href="teacher_your_thesis_topics.php">Tez Konularınız</a>
</div>

<div id="nav">
	<div id="logo">
	<img src="images/logo.png" width="203" height="85" />
	</div>
	<div id="nav_inner">
    <a href="#">TEZ İŞLEMLERİ <span class="style2">▼</span>   </a> <a href="<?php echo $logoutAction ?>">GÜVENLİ ÇIKIŞ  </a></div>
	<div id="searchArea">
	<form action="teacher_displaypage.php" method="get" name="formSearch" id="formSearch" onsubmit="return validateSearchQueryString()">
		<input id="searchTextfield" name="q" type="text" <?php if(SAFE_GET('q')){?>value="<?=SAFE_GET('q')?>"<? } ?>/>
		<input name="ara" type="submit" id="searchButton" value=" " />
	</form>
	</div>
</div>
<div id="container">
		<div id="container_col_left">
		  <div id="menu">
			  <div class="navHeader_Main" href="#">BİTİRME TEZİ KONU SEÇİMİ </div> 
			  	<a class="buttonStyle" href="teacher_displaypage.php">Tüm Tez Konuları </a>
			    <?php do { ?>
			    <a class="buttonStyle" href="teacher_displaypage.php?division=<?php echo $row_divisions['divisionID']; ?>"><?php echo $row_divisions['divisionTitle']; ?></a>
			    <?php } while ($row_divisions = mysql_fetch_assoc($divisions)); ?><br />
			  <br />
		  </div>
		</div>
		<div id="container_col_middle">
		  <div class="NewsFeed">
		  	<div class="NFright">
			  <div class="introDisplayer">KİŞİSEL BİLGİLERİ DÜZENLE</div>
			  <div id="statusMessage"><?php if(SAFE_GET('update')=='success') {?>Güncelleme Başarılı<? }  ?></div>
		  	  <form id="form1" name="form1" method="POST" onsubmit="return formValidate()" action="<?php echo $editFormAction; ?>">
		  	    <table width="497" height="292" border="0" cellpadding="0" cellspacing="4">
                  <tr>
                    <td width="115" height="26" valign="top"><span class="style23">İsminiz</span></td>
                    <td width="370"><?php echo $row_loggeduser['teacherFullname']; ?></td>
                  </tr>
                  <tr>
                    <td valign="top"><span class="style23">Kullanıcı Adı </span></td>
                    <td><?php echo $row_loggeduser['teacherName']; ?></td>
                  </tr>
                  <tr>
                    <td valign="top"><span class="style23">Şifre</span></td>
                    <td><input name="teacherPassword" type="password" class="formTextfield2" id="teacherPassword" value="<?php echo $row_loggeduser['teacherPassword']; ?>" /><div class="validationInfo"></div></td>
                  </tr>
                  <tr>
                    <td valign="top"><span class="style23">Şifre Tekrar </span></td>
                    <td><input name="teacherPassword2" type="password" class="formTextfield2" id="teacherPassword2" value="<?php echo $row_loggeduser['teacherPassword']; ?>" /><div class="validationInfo"></div></td>
                  </tr>
                  <tr>
                    <td valign="top"><span class="style23">Email</span></td>
                    <td><input name="teacherEmail" type="text" class="formTextfield2" id="teacherEmail" value="<?php echo $row_loggeduser['teacherEmail']; ?>" />
                        <div class="validationInfo"></div></td>
                  </tr>
                  <tr>
                    <td height="113" valign="top"><span class="style23">Notlar</span></td>
                    <td valign="top"><p>
                        <textarea name="teacherNotes" cols="55" rows="15" class="formTextfield2" id="teacherNotes" style="height:70px;"><?php echo $row_loggeduser['teacherNotes']; ?></textarea><div class="validationInfo"></div>
                    </p>                        </td>
                  </tr>
                  <tr>
                    <td valign="top"><span class="style23">Anabilim Dalı </span></td>
                    <td><?php do {?>
                        <input name="teacherDivision" <?php if($row_divisions2['divisionID']==$row_loggeduser['teacherDivision']){ ?> checked="checked" <?php } ?> type="radio" value="<?=$row_divisions2['divisionID']?>" /><?=$row_divisions2['divisionTitle']?> <br />
						 <?php } while ($row_divisions2 = mysql_fetch_assoc($divisions2)); ?>
						<div class="validationInfo"></div></td>
                  </tr>
				  <tr>
                    <td valign="top"><span class="style23">Konularınıza birden fazla öğrenci kabul edilsin mi?</span></td>
                    <td>
                      <input name="teacherMultiple" <?php if($row_loggeduser['teacherMultiple']=="yes"){ ?>checked="checked"<?php } ?> type="radio" value="yes" />
                      Evet
                      <input name="teacherMultiple" <?php if($row_loggeduser['teacherMultiple']=="no"){ ?>checked="checked"<?php } ?> type="radio" value="no" />
                      Hayır<div class="validationInfo"></div></td>
                  </tr>
                  <tr>
                    <td height="33"><input name="teacherID" type="hidden" id="teacherID" value="<?php echo $row_loggeduser['teacherID']; ?>" />
                    <input name="validate" type="hidden" id="validate" value="yes" /></td>
                    <td><input type="submit" id="formGonder" class="buttonStyle buttonStyleSubmit" name="Submit" value="Onayla" /></td>
                  </tr>
                </table>
                <input type="hidden" name="MM_update" value="form1">
		  	  </form>
	  	    </div>
		  </div>	  
		</div>
		<div id="container_col_right"> 
		  <div class="NewsFeed">
		    <div class="NFrightProfile">
              <h2>ÖĞRETİM ÜYESİ BİLGİLERİ</h2>
		      <span class="introDisplayer"><span class="style3"><span class="style4"><?php echo $row_loggeduser['teacherName']; ?><br />
              <?php echo $row_loggeduser['teacherFullname']; ?></span><br />
              <?php echo $row_loggeduser['teacherEmail']; ?></span><br />
              <br />
		        <strong>Anabilim Dalı: </strong>
			<?php mysql_select_db($database_connect, $connect);
				$query_divisions3 = sprintf("SELECT * FROM divisions WHERE divisionID='%d'",$row_loggeduser['teacherDivision']);
				$divisions3 = mysql_query($query_divisions3, $connect) or die(mysql_error());
				$row_divisions3 = mysql_fetch_assoc($divisions3);
				$totalRows_divisions3 = mysql_num_rows($divisions3);
			?>
				<?php echo $row_divisions3['divisionTitle']; ?><br />
		        <br />
		        <strong>Notlar:</strong><br />
		        <?php echo $row_loggeduser['teacherNotes']; ?></span><br />
  <a href="teacher_edit_personal_info.php">[Düzenle]</a></div>
		  </div>
</div></div><!-- Burası -->
<center>
	<div id="footer">
	  <div id="footer_col_left">
		  <center>
		    <p align="left"><a href="index.php">Öğrenci Giriş </a></p>
		  </center>
	  </div>
		<div id="footer_col_middle">
			<center>
			  <p>Copyright &#169; Bitirme OGÜ 2013</p>
		  </center>
		</div>
		<div id="footer_col_right">
		  <div align="right">
		  	<a href="#"><img src="images/logo_footer.png" width="120" height="36" border="0" /></a> </div>
	  </div>
	</div>
</center>
</div>
</body>
</html>
<?php
mysql_free_result($divisions);

mysql_free_result($loggeduser);

mysql_free_result($divisions2);

mysql_free_result($divisions3);
?>
