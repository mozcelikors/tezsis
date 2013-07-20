<!-- 
/************************
 * TezSis: TezYonetimSistemi
 *
 * @brief Akademisyenler ve ögrenciler arasindaki arayüzü saglayarak tez islemlerini yürütmeyi saglayan PHP tabanli bir web sistemidir.
 * @version 1.1
 * @author Mustafa Özçelikörs <mozcelikors>
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
