<?php
/************************
 * TezSis: TezYonetimSistemi
 *
 * @brief Akademisyenler ve �grenciler arasindaki aray�z� saglayarak tez islemlerini y�r�tmeyi saglayan PHP tabanli bir web sistemidir.
 * @version 1.1
 * @author Mustafa �z�elik�rs <mozcelikors>
 * @contact mozcelikors@gmail.com
 * @website thewebblog.net
 ******/

# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_connect = "localhost";
$database_connect = "TezSis_veritabani";
$username_connect = "isim";
$password_connect = "sifre";
$connect = mysql_pconnect($hostname_connect, $username_connect, $password_connect) or trigger_error(mysql_error(),E_USER_ERROR); 
?>