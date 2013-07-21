<?php require_once('Connections/connect.php'); ?><?php require_once('modules/safety.php'); ?>
<?php
// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="index.php?err=numberDenied";
  $loginUsername = $_POST['studentNumber'];
  $LoginRS__query = "SELECT userSchoolno FROM users WHERE userSchoolno='" . $loginUsername . "'";
  mysql_select_db($database_connect, $connect);
  $LoginRS=mysql_query($LoginRS__query, $connect) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
    $MM_qsChar = "?";
    //append the username to the redirect page
    if (substr_count($MM_dupKeyRedirect,"?") >=1) $MM_qsChar = "&";
    $MM_dupKeyRedirect = $MM_dupKeyRedirect . $MM_qsChar ."requsername=".$loginUsername;
    header ("Location: $MM_dupKeyRedirect");
    exit;
  }
}

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO users (userPassword, userEmail, userAbout, userCreatedate, userFullname, userSchoolno) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['Email'], "text"),
                       GetSQLValueString($_POST['About'], "text"),
                       GetSQLValueString($_POST['createDate'], "text"),
                       GetSQLValueString($_POST['fullName'], "text"),
                       GetSQLValueString($_POST['studentNumber'], "text"));

  mysql_select_db($database_connect, $connect);
  $Result1 = mysql_query($insertSQL, $connect) or die(mysql_error());

  $insertGoTo = "index.php?success=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['pass'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "user_default.php";
  $MM_redirectLoginFailed = "index.php?err=accessDenied";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_connect, $connect);
  
  $LoginRS__query=sprintf("SELECT userSchoolno, userPassword FROM users WHERE userSchoolno='%s' AND userPassword='%s'",
    get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password)); 
   
  $LoginRS = mysql_query($LoginRS__query, $connect) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
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
<link rel="stylesheet" href="index_files/style.css" type="text/css" media="screen" charset="utf-8">
<title><?php include("info/info.inc"); ?></title>
<style type="text/css">
<!--

body {/*Sayfa kaydırılamasın*/
    overflow:hidden;
}
a{color:CCC;}
a:hover{color:red; text-decoration:none;}
.style20 {font-size: 12px; color: #333333; }
.style21 {
	color: #666666;
	font-weight: bold;
}
.validationInfo {
	display:inline;
	font-size:11px;
}
.redInfo {
	padding-left:15px; color:#F00;
}
.greenInfo {
	padding-left:15px; color:#669900;
}
.style22 {
	color: #669900;
	font-size: 12px;
}
-->
        </style>
<script src="js/jquery-1.6.4.js" type="text/javascript" language="javascript"></script>
<script src="js/formvalidations.js" type="text/javascript" language="javascript"></script>
<script src="js/formpasif.js" type="text/javascript" language="javascript"></script>
<script src="js/emailvalidations_index.js" type="text/javascript" language="javascript"></script>
</head>
<body onload="if (top != self) document.write('<body style=\'background:transparent;\'></body>');">
<div align="center"><br />
  <br />
<img src="index_files/images/bitirmelogo.png" width="400" height="100" /><br />
<br />
<table width="580" height="390" border="0" cellpadding="5" cellspacing="5">
  <tr>
    <td width="50%" height="380" valign="top">
	  <p class="style21">Öğrenci Kaydı </p>
	  
	    <?php if(SAFE_GET('success')==1){ ?>
	    <p><span class="style22">Kayıt Başarılı.</span><br /></p>
	    <?php } ?>
	    <?php if(SAFE_GET('err')=='numberDenied'){ ?>
	   <p> <span class="style22">Ögrenci numaranız ile yapılmış bir kayıt zaten var.</span><br /></p>
	    <?php } ?>
	    
	  <form id="form1" name="form1" method="POST"  onsubmit="return validateRegisterUser()" action="<?php echo $editFormAction; ?>">
	    <table width="275" height="22" border="0" cellpadding="0" cellspacing="4">
	      <tr>
            <td width="116" valign="top"><span class="style20">Öğrenci Numarası </span></td>
	        <td width="147"><input name="studentNumber" type="text" class="formTextfield2" id="studentNumber" />
			<div class="validationInfo"></div></td>
	        </tr>
	      <tr>
            <td valign="top"><span class="style20">Tam İsim</span></td>
	        <td><input name="fullName" type="text" class="formTextfield2" id="fullName" />
			<div class="validationInfo"></div></td>
	        </tr>
	      <tr>
            <td valign="top"><span class="style20">Şifre</span></td>
	        <td><input name="password" type="password" class="formTextfield2" id="password" />
			<div class="validationInfo"></div></td>
	        </tr>
	      <tr>
            <td valign="top"><span class="style20">Şifre Tekrar </span></td>
	        <td><input name="passwordRepeat" type="password" class="formTextfield2" id="passwordRepeat" />
			<div class="validationInfo"></div></td>
	        </tr>
	      <tr>
            <td valign="top"><span class="style20">Email</span></td>
	        <td><input name="Email" type="text" class="formTextfield2" id="Email" />
			<div class="validationInfo"></div></td>
	        </tr>
	      <tr>
            <td valign="top"><span class="style20">Kriterler</span></td>
	        <td><textarea name="About" cols="25" rows="5" id="About" style="font-family:'Tahoma',Arial; font-size:12px;"></textarea>
			<div class="validationInfo"></div></td>
	        </tr>
	      <tr>
	        <td height="33"><input name="createDate" type="hidden" id="createDate" value="<?php echo date(d.".".m.".".Y);?>" /></td>
	        <td><input type="submit" id="formGonder" name="Submit" value="Kayıt" /></td>
	        </tr>
        </table>
	    <input type="hidden" name="MM_insert" value="form1">
	  </form>	  </td>
    <td width="50%" align="left" valign="top"><p class="style21">Öğrenci Girişi</p>
	  <form id="form2" name="form2" method="POST" onsubmit="return validateUserLogin()" action="<?php echo $loginFormAction; ?>">
	    <table width="275" height="149" border="0" cellpadding="0" cellspacing="4">
          <tr>
            <td width="116" valign="top"><span class="style20">Öğrenci Numarası </span></td>
            <td width="147"><input name="username" type="text" class="formTextfield2" id="username" /></td>
          </tr>
          <tr>
            <td valign="top"><span class="style20">Şifre </span></td>
            <td><input name="pass" type="password" class="formTextfield2" id="pass" /></td>
          </tr>
  <td height="33">&nbsp;</td>
      <td><input type="submit" id="formGonder" name="Submit2" value="Giriş" /></td>
  </tr>
        </table>
	    <p>&nbsp;</p>
      </form>
      <p>&nbsp;</p></td>
  </tr>
</table>

</div>
</body>
</html>
<?php ob_end_flush(); ?>