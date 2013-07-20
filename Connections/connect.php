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

# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_connect = "localhost";
$database_connect = "TezSis_veritabani";
$username_connect = "isim";
$password_connect = "sifre";
$connect = mysql_pconnect($hostname_connect, $username_connect, $password_connect) or trigger_error(mysql_error(),E_USER_ERROR); 
?>