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

<?php require_once('Connections/connect.php'); ?>
<?php
// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="teacher_login.php?err=userAlreadyExists";
  $loginUsername = $_POST['teacherName'];
  $LoginRS__query = "SELECT teacherName FROM teachers WHERE teacherName='" . $loginUsername . "'";
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
//Şifre kontrol
if(SAFE_POST('MM_insert') && (SAFE_POST('registerPassword')==1314159)){
	$insert = 1;
}
if(SAFE_POST('MM_insert') && (SAFE_POST('registerPassword')!=1314159)){
	$insert = -1;
}
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if($insert == 1){
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO teachers (teacherName, teacherPassword, teacherFullname, teacherEmail, teacherJoineddate, teacherNotes, teacherDivision, teacherMultiple) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['teacherName'], "text"),
                       GetSQLValueString($_POST['teacherPassword'], "text"),
                       GetSQLValueString($_POST['teacherFullname'], "text"),
                       GetSQLValueString($_POST['teacherEmail'], "text"),
                       GetSQLValueString($_POST['teacherJoineddate'], "text"),
                       GetSQLValueString($_POST['teacherNotes'], "text"),
                       GetSQLValueString($_POST['teacherDivision'], "int"),
                       GetSQLValueString($_POST['teacherMultiple'], "text"));

  mysql_select_db($database_connect, $connect);
  $Result1 = mysql_query($insertSQL, $connect) or die(mysql_error());

  $insertGoTo = "teacher_login.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
} // if($insert == 1){
?><?php require_once('modules/safety.php'); ?>
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
  $MM_redirectLoginSuccess = "teacher_default.php";
  $MM_redirectLoginFailed = "error.php?err=accessDenied";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_connect, $connect);
  
  $LoginRS__query=sprintf("SELECT teacherName, teacherPassword FROM teachers WHERE teacherName='%s' AND teacherPassword='%s'",
    get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password)); 
   
  $LoginRS = mysql_query($LoginRS__query, $connect) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
    //declare two session variables and assign them
    $_SESSION['MM_Teachername'] = $loginUsername;
    $_SESSION['MM_TeacherGroup'] = $loginStrGroup;	      

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
?><?php 
function SAFE_POST($theVar){
	$returnVal = addslashes(strip_tags($_POST[$theVar]));
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
<title>Bitirme Ogü</title>
<style type="text/css">
<!--

body {/*Sayfa kaydırılamasın*/
    overflow:hidden;
}
.style15 {font-family: Tahoma, Arial, 'Helvetica Neue', Helvetica, sans-serif}
a{color:CCC;}
a:hover{color:red; text-decoration:none;}
.style20 {font-size: 12px; color: #333333; }
.style21 {
	color: #666666;
	font-weight: bold;
}
.style22 {
	color: #669900;
	font-size: 12px;
}
.style24 {color: #FF0000; font-size: 12px; }
.validationInfo {
	display:inline;
}
.redInfo {
	padding-left:15px; color:#F00; font-size:11px;
}
.greenInfo {
	padding-left:15px; color:#669900; font-size:11px;
}
-->
        </style>
<script src="js/jquery-1.6.4.js" type="text/javascript" language="javascript"></script>
<script src="js/formvalidations.js" type="text/javascript" language="javascript"></script>
<script src="js/formpasif.js" type="text/javascript" language="javascript"></script>
<script src="js/emailvalidations_teacher_login.js" type="text/javascript" language="javascript"></script>
</head>
<body onload="if (top != self) document.write('<body style=\'background:transparent;\'></body>');">
<div align="center"><br />
  <br />
<img src="index_files/images/bitirmelogo.png" width="400" height="100" /><br />
<br />
<table width="295" height="390" border="0" cellpadding="5" cellspacing="5">
  <tr>
    <td width="50%" height="380" align="left" valign="top"><p class="style21">Öğretim Üyesi Girişi</p>
	  <form ACTION="<?php echo $loginFormAction; ?>" id="form2" onsubmit ="return validateTeacherLogin()" name="form2" method="POST">
	    <table width="275" height="149" border="0" cellpadding="0" cellspacing="4">
	      <tr>
            <td width="116" valign="top"><span class="style20">Kullanıcı Adı  </span></td>
	        <td width="147"><input name="username" type="text" class="formTextfield2" id="username" /></td>
	        </tr>
	      <tr>
            <td valign="top"><span class="style20">Şifre </span></td>
	        <td><input name="pass" type="password" class="formTextfield2" id="pass" /></td>
	      </tr>
            <td height="33">&nbsp;</td>
            <td><input type="submit" id="formGonder" name="Submit" value="Giriş" /></td>
          </tr>
        </table>
	    <p>&nbsp;</p>
      </form>
      <p>&nbsp;</p></td>
    <td width="50%" align="left" valign="top"><p><span class="style21">Öğretim Üyesi Kayıt </span></p>
	<?php if($insert == 1){ ?>
	    <p><span class="style22">Kayıt Başarılı.</span><br /></p>
	    <?php } ?>
        <?php if($insert == -1){ ?>
        <p><span class="style24">Kayıt Şifresi Yanlış. </span><br />
        </p>
        <?php } ?>
		<?php if(SAFE_GET('err') == "userAlreadyExists"){ ?>
        <p><span class="style24">Bu Kullanıcı İsmi Başkası Tarafından Kullanılıyor. </span><br />
        </p>
        <?php } ?>

<form id="form1" name="form1" onsubmit="return validateTeacherRegister()" method="POST" action="<?php echo $editFormAction; ?>">
        <table width="275" height="22" border="0" cellpadding="0" cellspacing="4">
          <tr>
            <td width="116" valign="top"><span class="style20">Kayıt Şifresi </span></td>
            <td width="147"><input name="registerPassword" type="text" class="formTextfield2" id="registerPassword" />
                <div class="validationInfo"></div></td>
          </tr>
          <tr>
            <td valign="top"><span class="style20">Ünvan/isim</span></td>
            <td><input name="teacherFullname" type="text" class="formTextfield2" id="teacherFullname" />
                <div class="validationInfo"></div></td>
          </tr>
          <tr>
            <td valign="top"><span class="style20">Kullanıcı Adı </span></td>
            <td><input name="teacherName" type="password" class="formTextfield2" id="teacherName" />
                <div class="validationInfo"></div></td>
          </tr>
          <tr>
            <td valign="top"><span class="style20">Şifre</span></td>
            <td><input name="teacherPassword" type="password" class="formTextfield2" id="teacherPassword" />
                <div class="validationInfo"></div></td>
          </tr>
          <tr>
            <td valign="top"><span class="style20">Şifre Tekrar </span></td>
            <td><input name="teacherPassword2" type="password" class="formTextfield2" id="teacherPassword2" />
                <div class="validationInfo"></div></td>
          </tr>
          <tr>
            <td valign="top"><span class="style20">Email</span></td>
            <td><input name="teacherEmail" type="text" class="formTextfield2" id="teacherEmail" />
                <div class="validationInfo"></div></td>
          </tr>
          <tr>
            <td valign="top"><span class="style20">Notlar</span></td>
            <td><textarea name="teacherNotes" cols="25" rows="5" id="teacherNotes" style="font-family:'Tahoma',Arial; font-size:12px;"></textarea>
                <div class="validationInfo"></div></td>
          </tr>
          <tr>
            <td height="33"><input name="teacherJoineddate" type="hidden" id="teacherJoineddate" value="<?php echo date(d.".".m.".".Y);?>" />
              <input name="teacherMultiple" type="hidden" id="teacherMultiple" value="yes" />
              <input name="teacherDivision" type="hidden" id="teacherDivision" value="1" /></td>
            <td><input type="submit" id="formGonder" name="Submit2" value="Kayıt" /></td>
          </tr>
        </table>
        <input type="hidden" name="MM_insert" value="form1">
      </form>
      </td>
  </tr>
</table>

</div>
<div class="icerik_outermost style15">

</div>
</body>
</html>
<?php ob_end_flush(); ?>