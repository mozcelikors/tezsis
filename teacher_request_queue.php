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

// thesis1
$maxRows_thesis1 = 10;
$pageNumThesisOne = 0;
if (isset($_GET['pageNumThesisOne'])) {
  $pageNumThesisOne = SAFE_GET('pageNumThesisOne');
}
$startRow_thesis1 = $pageNumThesisOne * $maxRows_thesis1;
mysql_select_db($database_connect, $connect);
$loggedUserID = $row_loggeduser['teacherID'];
$query_thesis1 = sprintf("SELECT * FROM requests WHERE requestRelatedTeacherID='%d' ORDER BY requestID ASC", $loggedUserID);
$query_limit_thesis1 = sprintf("%s LIMIT %d, %d", $query_thesis1, $startRow_thesis1, $maxRows_thesis1);
$thesis1 = mysql_query($query_limit_thesis1, $connect) or die(mysql_error());
$row_thesis1 = mysql_fetch_assoc($thesis1);

if (isset($_GET['totalRowsThesisOne'])) {
  $totalRowsThesisOne = SAFE_GET('totalRowsThesisOne');
} else {
  $all_thesis1 = mysql_query($query_thesis1);
  $totalRowsThesisOne = mysql_num_rows($all_thesis1);
}
$totalPages_thesis1 = ceil($totalRowsThesisOne/$maxRows_thesis1)-1;
// ---------------- thesis1 bitiş
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
// İstek Onaylama Başlangıç ---------------------------------------------------
$requestToBeConfirmed = SAFE_GET('confirmRequestID');
$confirm = SAFE_GET('confirm');
// Güvenlik önlemi
if( ($confirm==1) && (!is_numeric($requestToBeConfirmed))){ exit(0); }
// Silinecek request in user kontrolünü yapalım.
mysql_select_db($database_connect, $connect);
$query_confirmerTeacher = sprintf("SELECT * FROM requests WHERE requestID='%d' AND requestRelatedTeacherID='%d'", $requestToBeConfirmed, $row_loggeduser['teacherID']);
$confirmerTeacher = mysql_query($query_confirmerTeacher, $connect) or die(mysql_error());
$row_confirmerTeacher = mysql_fetch_assoc($confirmerTeacher);
$totalRows_confirmerTeacher = mysql_num_rows($confirmerTeacher);


//// Tezin bağlı olduğu hoca çoklu seçime izin veriyor mu vermiyor mu ona bakalım Başlangıç --------------
//Güvenlik kontrolleri
$thesisTeacherID = $row_confirmerTeacher['requestRelatedTeacherID'] ;
if(($confirm==1) && ($thesisTeacherID != $row_loggeduser['teacherID'])) exit(1);
//Çoklu seçim için sorgu yapalım
mysql_select_db($database_connect, $connect);
$query_checkMultipleBehaviour = sprintf("SELECT * FROM teachers WHERE teacherID = '%d'", $thesisTeacherID);
$checkMultipleBehaviour = mysql_query($query_checkMultipleBehaviour, $connect) or die(mysql_error());
$row_checkMultipleBehaviour = mysql_fetch_assoc($checkMultipleBehaviour);
$totalRows_checkMultipleBehaviour = mysql_num_rows($checkMultipleBehaviour);
if( $row_checkMultipleBehaviour['teacherMultiple']=="yes" ){
	$isMultiple = 1;
}elseif($row_checkMultipleBehaviour['teacherMultiple']=="no" ){
	$isMultiple = -1;
}else{
	// Bu bölge çalıştırılırsa
	// multiple Kısmında hata var demektir.
}
// $isMultiple==1 çoklu seçime izin veriyor, $isMultiple=-1 çoklu seçime izin vermiyor.
// $isMultiple==-1 seçeneği için gerçekleşecek durum için alttaki koşul içerisinde ayrı bir sorgu oluşturuyoruz.
// $isMultiple==-1 durumunda kabul edilen tez 'Uygun Değil' durumuna çevirilecek (güncellenecek).
//// -------Tezin bağlı olduğu hoca çoklu seçime izin veriyor mu vermiyor mu ona bakalım bitiş

if(($confirm == 1) && ($totalRows_confirmerTeacher>0) ){
	$toBeConfirmed2 = sprintf("INSERT INTO `accepted_requests` (
							`arequestID` ,
							`arequestThesisID` ,
							`arequestRelatedTeacherID` ,
							`arequestStudentID`
							)
							VALUES (
							NULL , '%d', '%d', '%d'
							);
", $row_confirmerTeacher['requestThesisID'], $row_confirmerTeacher['requestRelatedTeacherID'], $row_confirmerTeacher['requestStudentID']);
	$toBeConfirmed = sprintf("DELETE FROM requests WHERE requestStudentID='%d'", $row_confirmerTeacher['requestStudentID']);
	$multipleCaseQuery = sprintf("UPDATE `thesis` SET `thesisStatus` = 'Uygun Degil' WHERE `thesisID` ='%d'", $row_confirmerTeacher['requestThesisID']);
	if( $isMultiple == -1 ){
		mysql_query($multipleCaseQuery);
	}
	if( mysql_query($toBeConfirmed) && mysql_query($toBeConfirmed2)){
		// Sorgular başarılı ise Kullanıcıya Email Gönderiyoruz...
		// Request'in bağlı olduğu user'ı sorgulayalım.
		mysql_select_db($database_connect, $connect);
		$query_senderuser = sprintf("SELECT * FROM users WHERE userID = '%s'", $row_confirmerTeacher['requestStudentID']);
		$senderuser = mysql_query($query_senderuser, $connect) or die(mysql_error());
		$row_senderuser = mysql_fetch_assoc($senderuser);
		$totalRows_senderuser = mysql_num_rows($senderuser);
		
			$fromEmail=$row_loggeduser['teacherEmail'];
			$toEmail=$row_senderuser['userEmail'];
			$toName=$row_senderuser['userFullname'];
  			$postaKontrol=filter_var($toEmail,FILTER_VALIDATE_EMAIL);
  			if($toEmail!="" && $fromEmail!="" && $postaKontrol){
 			$message="
			------------------------------------------------------------------------
			Bu mail Bitirme OGU otomatik mail sistemi araciligi ile yollanmaktadir.
			------------------------------------------------------------------------
			
			Degerli uyemiz {$toName},
			Yaptiginiz bir bitirme basvurusu, ogretim uyesi tarafindan kabul edilmistir. Lutfen bu bilgiyi once web sisteminden kontrol ediniz, daha sonra da ogretim uyesi ile iletisime geciniz.
			
			------------------------------------------------------------------------
			";		
			$headers=sprintf("from: %s <%s>",$row_loggeduser['teacherFullname'],$row_loggeduser['teacherEmail']);
			$headers.="Content-Type: Bitirme OGU otomatik mail sistemi ; charset=ISO-8859-9\n";
			$headers .="MIME-Version: 1.0\n";
			$mailYolla=mail($toEmail,"Bitirme OGU Otomatik E-Mail",$message,$headers); 
  			if(!$mailYolla){ $mailSent = 1; }
			}elseif($mailYolla){ $mailSent = -1;}

		// Ardından sayfamızı yeniliyoruz
		header( "refresh:0; url=teacher_request_queue.php" );
	}
}
// -------------------------------------------------------- İstek Onaylama Bitiş
?>
<?php 
// İstek Reddetme Başlangıç ---------------------------------------------------
$requestToBeDenied = SAFE_GET('denyRequestID');
$deny = SAFE_GET('deny');
// Güvenlik önlemi
if( ($deny==1) && (!is_numeric($requestToBeDenied))){ exit(0); }
// Silinecek request in user kontrolünü yapalım.
mysql_select_db($database_connect, $connect);
$query_denierTeacher = sprintf("SELECT * FROM requests WHERE requestID='%d' AND requestRelatedTeacherID='%d'", $requestToBeDenied, $row_loggeduser['teacherID']);
$denierTeacher = mysql_query($query_denierTeacher, $connect) or die(mysql_error());
$row_denierTeacher = mysql_fetch_assoc($denierTeacher);
$totalRows_denierTeacher = mysql_num_rows($denierTeacher);
if(($deny == 1) && ($totalRows_denierTeacher>0) ){
	$toBeDenied2 = sprintf("INSERT INTO `refused_requests` (
							`rrequestID` ,
							`rrequestThesisID` ,
							`rrequestRelatedTeacherID` ,
							`rrequestStudentID`
							)
							VALUES (
							NULL , '%d', '%d', '%d'
							);
", $row_denierTeacher['requestThesisID'], $row_denierTeacher['requestRelatedTeacherID'], $row_denierTeacher['requestStudentID']);
	$toBeDenied = sprintf("DELETE FROM requests WHERE requestStudentID='%d' AND requestID='%d' AND requestRelatedTeacherID='%d'", $row_denierTeacher['requestStudentID'], $row_denierTeacher['requestID'], $row_loggeduser['teacherID']);
	if( mysql_query($toBeDenied) && mysql_query($toBeDenied2)){
		// Sorgular başarılı ise Kullanıcıya Email Gönderiyoruz...
		// Request'in bağlı olduğu user'ı sorgulayalım.
		mysql_select_db($database_connect, $connect);
		$query_senderuser = sprintf("SELECT * FROM users WHERE userID = '%s'", $row_denierTeacher['requestStudentID']);
		$senderuser = mysql_query($query_senderuser, $connect) or die(mysql_error());
		$row_senderuser = mysql_fetch_assoc($senderuser);
		$totalRows_senderuser = mysql_num_rows($senderuser);
		
			$fromEmail=$row_loggeduser['teacherEmail'];
			$toEmail=$row_senderuser['userEmail'];
			$toName=$row_senderuser['userFullname'];
  			$postaKontrol=filter_var($toEmail,FILTER_VALIDATE_EMAIL);
  			if($toEmail!="" && $fromEmail!="" && $postaKontrol){
 			$message="
			------------------------------------------------------------------------
			Bu mail Bitirme OGU otomatik mail sistemi araciligi ile yollanmaktadir.
			------------------------------------------------------------------------
			
			Degerli uyemiz {$toName},
			Yaptiginiz bir bitirme basvurusu, ogretim uyesi tarafindan REDDEDILMISTIR. Lutfen bu bilgiyi once web sisteminden kontrol ediniz, daha sonra da yeni bitirme konularina istekte bulununuz.
			
			------------------------------------------------------------------------
			";		
			$headers=sprintf("from: %s <%s>",$row_loggeduser['teacherFullname'],$row_loggeduser['teacherEmail']);
			$headers.="Content-Type: Bitirme OGU otomatik mail sistemi ; charset=ISO-8859-9\n";
			$headers .="MIME-Version: 1.0\n";
			$mailYolla=mail($toEmail,"Bitirme OGU Otomatik E-Mail",$message,$headers); 
  			if(!$mailYolla){ $mailSent = 1; }
			}elseif($mailYolla){ $mailSent = -1;}
		
		// Email gonderdikten sonra yonlendirme yapalim.
		header( "refresh:0; url=teacher_request_queue.php" );
	}
}
// -------------------------------------------------------- İstek Reddetme Bitiş
?>
<?php 
// Accepted Request Silme Başlangıç ---------------------------------------------------
$requestToBeDeleted = SAFE_GET('arequestThesisID');
$arequest_delete = SAFE_GET('arequest_delete');
// Güvenlik önlemi
if( ($arequest_delete ==1) && (!is_numeric($requestToBeDeleted))){ exit(0); }
// Silinecek request in user kontrolünü yapalım.
mysql_select_db($database_connect, $connect);
$query_deleterUser = sprintf("SELECT * FROM accepted_requests WHERE arequestThesisID='%d' AND arequestRelatedTeacherID='%d'", $requestToBeDeleted, $row_loggeduser['teacherID']);
$deleterUser = mysql_query($query_deleterUser, $connect) or die(mysql_error());
$row_deleterUser = mysql_fetch_assoc($deleterUser);
$totalRows_deleterUser = mysql_num_rows($deleterUser);
if(($arequest_delete == 1) && ($totalRows_deleterUser==1) ){
	$toBeDeleted = sprintf("DELETE FROM accepted_requests WHERE arequestThesisID='%d' AND arequestRelatedTeacherID='%d'", $requestToBeDeleted, $row_loggeduser['teacherID']);
	if( mysql_query($toBeDeleted) ){
		// Eğer silme işlemi başarılıysa sayfayı refresh edelim.
		header( "refresh:0; url=teacher_request_queue.php" );
	}
}
// -------------------------------------------------------- Accepted Request Silme Bitiş
?>
<?php 
// Refused Request Silme Başlangıç ---------------------------------------------------
$requestToBeDeleted = SAFE_GET('rrequestThesisID');
$rrequest_delete = SAFE_GET('rrequest_delete');
// Güvenlik önlemi
if( ($rrequest_delete ==1) && (!is_numeric($requestToBeDeleted))){ exit(0); }
// Silinecek request in user kontrolünü yapalım.
mysql_select_db($database_connect, $connect);
$query_deleterUser = sprintf("SELECT * FROM refused_requests WHERE rrequestThesisID='%d' AND rrequestRelatedTeacherID='%d'", $requestToBeDeleted, $row_loggeduser['teacherID']);
$deleterUser = mysql_query($query_deleterUser, $connect) or die(mysql_error());
$row_deleterUser = mysql_fetch_assoc($deleterUser);
$totalRows_deleterUser = mysql_num_rows($deleterUser);
if(($rrequest_delete == 1) && ($totalRows_deleterUser==1) ){
	$toBeDeleted = sprintf("DELETE FROM refused_requests WHERE rrequestThesisID='%d' AND rrequestRelatedTeacherID='%d'", $requestToBeDeleted, $row_loggeduser['teacherID']);
	if( mysql_query($toBeDeleted) ){
		// Eğer silme işlemi başarılıysa sayfayı refresh edelim.
		header( "refresh:0; url=teacher_request_queue.php" );
	}
}
// -------------------------------------------------------- Refused Request Silme Bitiş
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

<title>Bitirme Ogü</title>

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
<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen"/>
<script src="js/jquery-1.6.4.js" type="text/javascript" language="javascript"></script>
<script src="js/menu_animation.js" type="text/javascript" language="javascript"></script>
<script src="js/random_animation.js" type="text/javascript" language="javascript"></script>
<script src="js/formvalidations.js" type="text/javascript" language="javascript"></script>
<script src="js/formpasif.js" type="text/javascript" language="javascript"></script>

<script type="text/javascript" src="js/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$("a.fancyboxOpen").fancybox();
});
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
			  <div class="introDisplayer">ONAY BEKLEYEN KONU İSTEKLERİ</div>
		      <div class="newsTable">
		        <table class="displayerTable" width="617" border="0" cellspacing="1" cellpadding="4">
                  <tr>
                    <td width="19" height="22"><strong>No</strong></td>
                    <td width="125"><strong>Durum</strong></td>
                    <td width="227"><strong>Bitirme Konusu </strong></td>
                    <td width="110"><strong>Başvuran Öğrenci </strong></td>
                    <td width="80"><strong>Eklenme</strong></td>
                  </tr>
				  <?php
				  // Kabul edilen istek ---------------------------------------------------
				  
				    mysql_select_db($database_connect, $connect);
					$query_acceptedRequest = sprintf("SELECT * FROM accepted_requests WHERE arequestRelatedTeacherID='%d'",$row_loggeduser['teacherID']);
					$acceptedRequest = mysql_query($query_acceptedRequest, $connect) or die(mysql_error());
					$row_acceptedRequest = mysql_fetch_assoc($acceptedRequest);
					$totalRows_acceptedRequest = mysql_num_rows($acceptedRequest);
				  ?>
            <?php  do{
				  //Request bilgisini kullanarak tezi sorgulayıp getirelim
					mysql_select_db($database_connect, $connect);
					$query_requestedThesis = sprintf("SELECT * FROM thesis WHERE thesisID='%d'",$row_acceptedRequest['arequestThesisID']);
					$requestedThesis = mysql_query($query_requestedThesis, $connect) or die(mysql_error());
					$row_requestedThesis = mysql_fetch_assoc($requestedThesis);
					$totalRows_requestedThesis = mysql_num_rows($requestedThesis);
					if($totalRows_requestedThesis>=1){$bgColor = '#E2F9C1'; 
				   ?>
                  <tr style="background-color:<?=$bgColor?>;">
                    <td height="38">&nbsp;</td>
                    <td><strong>Kabul Edildi</strong><br />
					<a href="teacher_request_queue.php?arequest_delete=1&arequestThesisID=<?=$row_acceptedRequest['arequestThesisID']?>">[Sil]</a>
                    </td>
                    <td><?php echo $row_requestedThesis['thesisTopic']; ?><br />
						<div id="thesisCriteria"><strong>Tez Kriterleri: </strong><?php echo $row_requestedThesis['thesisCriteria']; ?></div></td>
                    <td><?php
					mysql_select_db($database_connect, $connect);
					$query_user = sprintf("SELECT * FROM users WHERE userID = '%d'", $row_acceptedRequest['arequestStudentID']);
					$user = mysql_query($query_user, $connect) or die(mysql_error());
					$row_user = mysql_fetch_assoc($user);
					$totalRows_user = mysql_num_rows($user);
					?><?=$row_user['userFullname']?><br /><a class="fancyboxOpen" href="teacher_user_display.php?userID=<?=$row_user['userID']?>">[İncele] </a></td>
                    <td><?php echo $row_requestedThesis['thesisDate']; ?></td>
                  </tr>
				   <?php } //if($totalRows_requestedThesis>=1) ?>
				  <?php } while ($row_acceptedRequest = mysql_fetch_assoc($acceptedRequest)); ?>
				 
                 <?php  //------------------------------------------kabul edilen istek bitiş   ?>
				 <?php
				  // Reddedilen istekler başlangıç---------------------------------------------------
				  
				    mysql_select_db($database_connect, $connect);
					$query_refusedRequest = sprintf("SELECT * FROM refused_requests WHERE rrequestRelatedTeacherID='%d'",$row_loggeduser['teacherID']);
					$refusedRequest = mysql_query($query_refusedRequest, $connect) or die(mysql_error());
					$row_refusedRequest = mysql_fetch_assoc($refusedRequest);
					$totalRows_refusedRequest = mysql_num_rows($refusedRequest);
				  ?>
            <?php  do{
				  //Request bilgisini kullanarak tezi sorgulayıp getirelim
					mysql_select_db($database_connect, $connect);
					$query_requestedThesis = sprintf("SELECT * FROM thesis WHERE thesisID='%d'",$row_refusedRequest['rrequestThesisID']);
					$requestedThesis = mysql_query($query_requestedThesis, $connect) or die(mysql_error());
					$row_requestedThesis = mysql_fetch_assoc($requestedThesis);
					$totalRows_requestedThesis = mysql_num_rows($requestedThesis);
					if($totalRows_requestedThesis>=1){$bgColor = '#FFD5D5'; 
				   ?>
                  <tr style="background-color:<?=$bgColor?>;">
                    <td height="38">&nbsp;</td>
                    <td><strong>Reddedildi</strong><br />
					<a href="teacher_request_queue.php?rrequest_delete=1&rrequestThesisID=<?=$row_refusedRequest['rrequestThesisID']?>">[Sil]</a></td>
                    <td><?php echo $row_requestedThesis['thesisTopic']; ?><br />
						<div id="thesisCriteria"><strong>Tez Kriterleri: </strong><?php echo $row_requestedThesis['thesisCriteria']; ?></div></td>
                    <td><?php
					mysql_select_db($database_connect, $connect);
					$query_user = sprintf("SELECT * FROM users WHERE userID = '%d'", $row_refusedRequest['rrequestStudentID']);
					$user = mysql_query($query_user, $connect) or die(mysql_error());
					$row_user = mysql_fetch_assoc($user);
					$totalRows_user = mysql_num_rows($user);
					?><?=$row_user['userFullname']?><br /><a class="fancyboxOpen" href="teacher_user_display.php?userID=<?=$row_user['userID']?>">[İncele] </a></td>
                    <td><?php echo $row_requestedThesis['thesisDate']; ?></td>
                  </tr>
				   <?php } //if($totalRows_requestedThesis>=1) ?>
				  <?php } while ($row_refusedRequest = mysql_fetch_assoc($refusedRequest)); ?>
				 
                 <?php  //-----------------------------------reddedilen istekler bitiş   ?>
				  <?php 
				  // Bekleyen istekler------------------------------------------------------
				  if($totalRowsThesisOne>0){
				  $i=0; do { if($i%2==0){$bgColor="#DDD";}else{ $bgColor="#D0D0D0"; }
				    mysql_select_db($database_connect, $connect);
					$query_requestedThesis = sprintf("SELECT * FROM thesis WHERE thesisID = '%d'", $row_thesis1['requestThesisID']);
					$requestedThesis = mysql_query($query_requestedThesis, $connect) or die(mysql_error());
					$row_requestedThesis = mysql_fetch_assoc($requestedThesis);
					$totalRows_requestedThesis = mysql_num_rows($requestedThesis);
					
					
				  ?>
                  <tr style="background-color:<?=$bgColor?>;">
                    <td height="38"><?=10*(int)SAFE_GET('pageNumThesisOne')+($i+1)?></td>
                    <td><strong>Cevap Bekleniyor</strong><br />
                      <a href="teacher_request_queue.php?confirm=1&confirmRequestID=<?=$row_thesis1['requestID']?>">Onayla</a> 
					  <a href="teacher_request_queue.php?deny=1&denyRequestID=<?=$row_thesis1['requestID']?>">Reddet</a>
					  </td>
                    <td><?php echo $row_requestedThesis['thesisTopic']; ?><br />
						<div id="thesisCriteria"><strong>Tez Kriterleri: </strong><?php echo $row_requestedThesis['thesisCriteria']; ?></div></td>
                    <td><?php
					mysql_select_db($database_connect, $connect);
					$query_user = sprintf("SELECT * FROM users WHERE userID = '%d'", $row_thesis1['requestStudentID']);
					$user = mysql_query($query_user, $connect) or die(mysql_error());
					$row_user = mysql_fetch_assoc($user);
					$totalRows_user = mysql_num_rows($user);
					?><?=$row_user['userFullname']?><br /><a class="fancyboxOpen" href="teacher_user_display.php?userID=<?=$row_user['userID']?>">[İncele] </a></td>
                    <td><?php echo $row_requestedThesis['thesisDate']; ?></td>
                  </tr>
                    <?php $i++; } while ($row_thesis1 = mysql_fetch_assoc($thesis1)); ?>
					<?php } // $totalRowsThesisOne>0 
					//------------------------------------bekleyen istekler bitiş    
					?>
                </table>
				 <?php if($totalRowsThesisOne>0){ ?>
				<div class="nextPrevSection">
				<?php if ($pageNumThesisOne > 0) { // Show if not first page ?>
			    <a href="<?php printf("%s?pageNumThesisOne=%d%s", $currentPage, max(0, $pageNumThesisOne - 1), $queryString_thesis1); ?>">&lt;&lt; Önceki</a>
				  <?php } // Show if not first page ?> 
				<?php if ($pageNumThesisOne < $totalPages_thesis1) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNumThesisOne=%d%s", $currentPage, min($totalPages_thesis1, $pageNumThesisOne + 1), $queryString_thesis1); ?>">Sonraki &gt;&gt;</a>
                  <?php } // Show if not last page ?> 
				</div>
				<?php }else{ echo "Hen&uuml;z bekleyen bir tez konusu iste&#287;iniz yok. "; } // $totalRowsThesisOne>0 ?>
			  </div>
			  
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

mysql_free_result($divisions3);
?>
