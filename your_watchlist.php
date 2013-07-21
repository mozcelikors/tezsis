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
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php";
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

$MM_restrictGoTo = "error.php?err=accessDenied";
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
$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_connect, $connect);
$query_divisions = "SELECT * FROM divisions ORDER BY divisionID ASC";
$divisions = mysql_query($query_divisions, $connect) or die(mysql_error());
$row_divisions = mysql_fetch_assoc($divisions);
$totalRows_divisions = mysql_num_rows($divisions);

$colname_loggeduser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_loggeduser = (get_magic_quotes_gpc()) ? $_SESSION['MM_Username'] : addslashes($_SESSION['MM_Username']);
}
mysql_select_db($database_connect, $connect);
$query_loggeduser = sprintf("SELECT * FROM users WHERE userSchoolno = '%s'", $colname_loggeduser);
$loggeduser = mysql_query($query_loggeduser, $connect) or die(mysql_error());
$row_loggeduser = mysql_fetch_assoc($loggeduser);
$totalRows_loggeduser = mysql_num_rows($loggeduser);

?>
<?php 
// thesis1
$maxRows_thesis1 = 10;
$pageNumThesisOne = 0;
if (isset($_GET['pageNumThesisOne'])) {
  $pageNumThesisOne = SAFE_GET('pageNumThesisOne');
}
$startRow_thesis1 = $pageNumThesisOne * $maxRows_thesis1;
mysql_select_db($database_connect, $connect);
$loggedUserID = $row_loggeduser['userID'];
$query_thesis1 = sprintf("SELECT * FROM watchlist WHERE watchlistStudentID='%d' ORDER BY watchlistID ASC", $loggedUserID);
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
// Watchlist'ten kaldırma Başlangıç ---------------------------------------------------
$requestToBeDeleted = SAFE_GET('watchlistItemID');
$delete = SAFE_GET('delete');
// Güvenlik önlemi
if( ($delete==1) && (!is_numeric($requestToBeDeleted))){ exit(0); }
// Silinecek request in user kontrolünü yapalım.
mysql_select_db($database_connect, $connect);
$query_deleterUser = sprintf("SELECT * FROM watchlist WHERE watchlistThesisID='%d' AND watchlistStudentID='%d'", $requestToBeDeleted, $row_loggeduser['userID']);
$deleterUser = mysql_query($query_deleterUser, $connect) or die(mysql_error());
$row_deleterUser = mysql_fetch_assoc($deleterUser);
$totalRows_deleterUser = mysql_num_rows($deleterUser);
if(($delete == 1) && ($totalRows_deleterUser==1) ){
	$toBeDeleted = sprintf("DELETE FROM watchlist WHERE watchlistThesisID='%d' AND watchlistStudentID='%d'", $requestToBeDeleted, $row_loggeduser['userID']);
	if( mysql_query($toBeDeleted) ){
		// Eğer silme işlemi başarılıysa sayfayı refresh edelim.
		header( "refresh:0; url=your_watchlist.php" );
	}
}
// -------------------------------------------------------- Watchlist'ten kaldırma Bitiş
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
	<a class="buttonStyle buttonStylePopUp" href="edit_personal_info.php">Kişisel Bilgileri Düzenle</a>
	<a class="buttonStyle buttonStylePopUp" href="request_queue.php">Bekleyen İstekler</a>
	<a class="buttonStyle buttonStylePopUp" href="your_watchlist.php">İzlemeye Alınanlar</a>
</div>

<div id="nav">
	<div id="logo">
	<img src="images/logo.png" width="203" height="85" />
	</div>
	<div id="nav_inner">
    <a href="#">TEZ İŞLEMLERİ <span class="style2">▼</span>   </a> <a href="<?php echo $logoutAction ?>">GÜVENLİ ÇIKIŞ  </a></div>
	<div id="searchArea">
	<form action="user_default.php" method="get" name="formSearch" id="formSearch" onsubmit="return validateSearchQueryString()">
		<input id="searchTextfield" name="q" type="text" <?php if(SAFE_GET('q')){?>value="<?=SAFE_GET('q')?>"<? } ?>/>
		<input name="ara" type="submit" id="searchButton" value=" " />
	</form>
	</div>
</div>
<div id="container">
		<div id="container_col_left">
		  <div id="menu">
			  <div class="navHeader_Main" href="#">BİTİRME TEZİ KONU SEÇİMİ </div> 
			  	<a class="buttonStyle" href="user_default.php">Tüm Tez Konuları </a>
			    <?php do { ?>
			    <a class="buttonStyle" href="user_default.php?division=<?php echo $row_divisions['divisionID']; ?>"><?php echo $row_divisions['divisionTitle']; ?></a>
			    <?php } while ($row_divisions = mysql_fetch_assoc($divisions)); ?><br />
			  <br />
		  </div>
		</div>
		<div id="container_col_middle">
		  <div class="NewsFeed">
		  	<div class="NFright">
			  <div class="introDisplayer">İZLEMEYE ALINANLAR</div>
			   <?php if($totalRowsThesisOne>0){ ?>
		      <div class="newsTable">
		        <table class="displayerTable" width="617" border="0" cellspacing="1" cellpadding="4">
                  <tr>
                    <td width="19" height="22"><strong>No</strong></td>
                    <td width="125"><strong>Durum</strong></td>
                    <td width="227"><strong>Bitirme Konusu </strong></td>
                    <td width="110"><strong>Öğretim Üyesi </strong></td>
                    <td width="80"><strong>Eklenme</strong></td>
                  </tr>
				  <?php
				  // İzleme Listesi Başlangıç --------------------------------------
				    
				  ?>
                  <?php $i=0; do { if($i%2==0){$bgColor="#DDD";}else{ $bgColor="#D0D0D0"; }
				  	mysql_select_db($database_connect, $connect);
					$query_requestedThesis = sprintf("SELECT * FROM thesis WHERE thesisID = '%d'", $row_thesis1['watchlistThesisID']);
					$requestedThesis = mysql_query($query_requestedThesis, $connect) or die(mysql_error());
					$row_requestedThesis = mysql_fetch_assoc($requestedThesis);
					$totalRows_requestedThesis = mysql_num_rows($requestedThesis);
				   ?>
                  <tr style="background-color:<?=$bgColor?>;">
                    <td height="38"><?=10*(int)SAFE_GET('pageNumThesisOne')+($i+1)?></td>
                    <td>
                        <?php if($row_requestedThesis['thesisStatus']=="Uygun"){ ?><strong>Uygun</strong><br /><a href="send_thesis_request.php?thesisID=<?php echo $row_requestedThesis['thesisID']; ?>">İstek Gönder</a> <br /><?php }else{ ?><strong>Uygun Değil</strong><br /><?php } ?>
					   <a style="color:#339966;" href="your_watchlist.php?delete=1&watchlistItemID=<?=$row_thesis1['watchlistThesisID']?>">İzlemeyi Kaldır</a></td>
                    <td><?php echo $row_requestedThesis['thesisTopic']; ?><br />
						<div id="thesisCriteria"><?php echo $row_requestedThesis['thesisCriteria']; ?></div></td>
                    <td><?php
					mysql_select_db($database_connect, $connect);
					$query_thesisUser = sprintf("SELECT * FROM teachers WHERE teacherID = '%d'",$row_requestedThesis['thesisTeacherID']);
					$thesisUser = mysql_query($query_thesisUser, $connect) or die(mysql_error());
					$row_thesisUser = mysql_fetch_assoc($thesisUser);
					$totalRows_thesisUser = mysql_num_rows($thesisUser);
					echo $row_thesisUser['teacherFullname'];
					?></td>
                    <td><?php echo $row_requestedThesis['thesisDate']; ?></td>
                  </tr>
                    <?php $i++; } while ($row_thesis1 = mysql_fetch_assoc($thesis1)); 
					//--------------------------------------------- İzleme Listesi Bitiş    ?>
                </table>
				<div class="nextPrevSection">
				<?php if ($pageNumThesisOne > 0) { // Show if not first page ?>
			    <a href="<?php printf("%s?pageNumThesisOne=%d%s", $currentPage, max(0, $pageNumThesisOne - 1), $queryString_thesis1); ?>">&lt;&lt; Önceki</a>
				  <?php } // Show if not first page ?> 
				<?php if ($pageNumThesisOne < $totalPages_thesis1) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNumThesisOne=%d%s", $currentPage, min($totalPages_thesis1, $pageNumThesisOne + 1), $queryString_thesis1); ?>">Sonraki &gt;&gt;</a>
                  <?php } // Show if not last page ?> 
				</div>
			  </div>
			  <?php }else{ echo "&#304;zlemeye al&#305;nan konular listeniz hen&uuml;z bo&#351;. "; } // $totalRowsThesisOne>0 ?>
	        </div>
		  </div>	  
		</div>
		<div id="container_col_right"> 
		  <div class="NewsFeed">
		    
		  	<div class="NFrightProfile">
			<h2>Öğrenci Bilgileri</h2>
			<span class="introDisplayer"><span class="style3"><span class="style4"><?php echo $row_loggeduser['userSchoolno']; ?><br />
			<?php echo $row_loggeduser['userFullname']; ?></span><br />
		    <?php echo $row_loggeduser['userEmail']; ?></span><br />
		    <br />
              <?php echo $row_loggeduser['userAbout']; ?></span><br />
		  	  <a href="edit_personal_info.php">[Düzenle]</a></div>
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
?>
