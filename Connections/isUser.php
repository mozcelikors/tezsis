<?php 
/************************
 * TezSis: TezYonetimSistemi
 *
 * @brief Akademisyenler ve ögrenciler arasindaki arayüzü saglayarak tez islemlerini yürütmeyi saglayan PHP tabanli bir web sistemidir.
 * @version 1.1
 * @author Mustafa Özçelikörs <mozcelikors>
 * @contact mozcelikors@gmail.com
 * @website thewebblog.net
 ******/

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