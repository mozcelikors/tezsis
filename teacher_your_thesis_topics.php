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

<?php ob_start(); ?>
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
// Thesis Topic'ten Silme Başlangıç ---------------------------------------------------
$requestToBeDeleted = SAFE_GET('thesisID');
$delete = SAFE_GET('delete');
// Güvenlik önlemi
if( ($delete==1) && (!is_numeric($requestToBeDeleted))){ exit(0); }
// Silinecek request in user kontrolünü yapalım.
mysql_select_db($database_connect, $connect);
$query_deleterUser = sprintf("SELECT * FROM thesis WHERE thesisID='%d' AND thesisTeacherID='%d'", $requestToBeDeleted, $row_loggeduser['teacherID']);
$deleterUser = mysql_query($query_deleterUser, $connect) or die(mysql_error());
$row_deleterUser = mysql_fetch_assoc($deleterUser);
$totalRows_deleterUser = mysql_num_rows($deleterUser);
if(($delete == 1) && ($totalRows_deleterUser==1) ){
	$toBeDeleted = sprintf("DELETE FROM thesis WHERE thesisID='%d' AND thesisTeacherID='%d'", $requestToBeDeleted, $row_loggeduser['teacherID']);
	if( mysql_query($toBeDeleted) ){
		// Eğer silme işlemi başarılıysa sayfayı refresh edelim.
		header( "refresh:0; url=teacher_your_thesis_topics.php" );
	}
}
// -------------------------------------------------------- Thesis Topic'ten Silme  Bitiş
?>
<?php 
// Uygunluk Değiştirme Başlangıç ---------------------------------------------------
$requestToBeToggled = SAFE_GET('thesisID');
$toggleGet = SAFE_GET('toggle');
// Güvenlik önlemi
if( (($toggleGet==1) || ($toggleGet==2)) && (!is_numeric($requestToBeToggled))){ exit(0); }
// Degistirilecek request in kontrolunu yapalim
mysql_select_db($database_connect, $connect);
$query_toggle = sprintf("SELECT * FROM thesis WHERE thesisID='%d' AND thesisTeacherID='%d'", $requestToBeToggled, $row_loggeduser['teacherID']);
$toggle = mysql_query($query_toggle, $connect) or die(mysql_error());
$row_toggle = mysql_fetch_assoc($toggle);
$totalRows_toggle = mysql_num_rows($toggle);
if((($toggleGet==1) || ($toggleGet==2)) && ($totalRows_toggle==1) ){
	if($toggleGet == 2){
		$toBeToggled = sprintf("UPDATE thesis SET thesisStatus='Uygun' WHERE thesisID='%d' AND thesisTeacherID='%d' AND thesisStatus='Uygun Degil'", $requestToBeToggled, $row_loggeduser['teacherID']);
	}elseif($toggleGet == 1){
		$toBeToggled = sprintf("UPDATE thesis SET thesisStatus='Uygun Degil' WHERE thesisID='%d' AND thesisTeacherID='%d' AND thesisStatus='Uygun'", $requestToBeToggled, $row_loggeduser['teacherID']);
	}
	if( mysql_query($toBeToggled) ){
		// Eğer silme işlemi başarılıysa sayfayı refresh edelim.
		header( "refresh:0; url=teacher_your_thesis_topics.php" );
	}
}
// -------------------------------------------------------- Uygunluk Değiştirme  Bitiş
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
<script src="js/jquery-1.6.4.js" type="text/javascript" language="javascript"></script>
<script src="js/menu_animation.js" type="text/javascript" language="javascript"></script>
<script src="js/random_animation.js" type="text/javascript" language="javascript"></script>
<script src="js/formvalidations.js" type="text/javascript" language="javascript"></script>
<script src="js/formpasif.js" type="text/javascript" language="javascript"></script>

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
			  <div class="introDisplayer">EKLEDİĞİNİZ TEZ KONULARI </div>
		      <div class="newsTable">
		        <table class="displayerTable" width="617" border="0" cellspacing="1" cellpadding="4">
                  <tr>
                    <td width="19" height="22"><strong>No</strong></td>
                      <td width="140"><strong>Bitirme Konusu </strong></td>
					  <td width="72"><strong>Uygunluk</strong></td>
                      <td width="250"><strong>Tez Kriterleri</strong></td>
                    <td width="80"><strong>Eklenme</strong></td>
                  </tr>
				  <?php 
				  
				     mysql_select_db($database_connect, $connect);
					$query_requestedThesis = sprintf("SELECT * FROM thesis WHERE thesisTeacherID = '%d'", $row_loggeduser['teacherID']);
					$requestedThesis = mysql_query($query_requestedThesis, $connect) or die(mysql_error());
					$row_requestedThesis = mysql_fetch_assoc($requestedThesis);
					$totalRows_requestedThesis = mysql_num_rows($requestedThesis);
					
				  // your thesis topics başlangıç------------------------------------------------------
				  ?>
				  
			<?php if($totalRows_requestedThesis>0){ ?>
				  <?php $i=0; do { if($i%2==0){$bgColor="#DDD";}else{ $bgColor="#D0D0D0"; }	  ?>

                  <tr style="background-color:<?=$bgColor?>;">
				  
                    <td height="38"><?=$i+1?></td>
                    <td><?php echo $row_requestedThesis['thesisTopic']; ?><br /><a href="teacher_edit_thesis.php?thesisID=<?php echo $row_requestedThesis['thesisID']; ?>">[Düzenle]</a>&nbsp;<a href="teacher_your_thesis_topics.php?delete=1&thesisID=<?php echo $row_requestedThesis['thesisID']; ?>">[Sil]</a></td>
					<td>
					<?php if($row_requestedThesis['thesisStatus']=='Uygun'){$toggleS = 1;}else{ $toggleS = 2;}?>
					<?php echo $row_requestedThesis['thesisStatus']; ?><br /><a href="teacher_your_thesis_topics.php?toggle=<?=$toggleS?>&thesisID=<?php echo $row_requestedThesis['thesisID']; ?>">[Uygunluğu Değiştir]</a></td>
                    <td>
						<div id="thesisCriteria"><?php echo $row_requestedThesis['thesisCriteria']; ?></div></td>
                    
                    <td><?php echo $row_requestedThesis['thesisDate']; ?></td>
                  </tr>
                    <?php $i++; } while ($row_requestedThesis = mysql_fetch_assoc($requestedThesis)); ?>
			<?php } // if($totalRows_requestedThesis>0){ ?>
					<?php
					//------------------------------------your thesis topics bitiş    
					?>
                </table>
				
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
<?php ob_end_flush(); ?>