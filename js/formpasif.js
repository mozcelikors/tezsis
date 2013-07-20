<!-- 
/************************
 * TezSis: TezYonetimSistemi
 *
 * @brief Akademisyenler ve �grenciler arasindaki aray�z� saglayarak tez islemlerini y�r�tmeyi saglayan PHP tabanli bir web sistemidir.
 * @version 1.1
 * @author Mustafa �z�elik�rs <mozcelikors>
 * @contact mozcelikors@gmail.com
 * @website thewebblog.net
 ******/
-->
 
if( jQuery ){
	$(document).ready(function(){
		$('form').submit(function(){
		$('input[type=submit]', this).attr('disabled', 'disabled');
		$('select', this).attr('disabled', 'disabled');
		$('input[type=text]', this).attr('readonly', 'readonly');
		$('textarea', this).attr('readonly', 'readonly');
		});
	})
}
