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
 
	function validateSearchQueryString(){
		if(!document.formSearch.q.value){
			alert("Bu alani bos birakamazsiniz. Guvenliginiz icin sayfayi yenilemeniz gerekmektedir.");
			return false;
		}
		return true;
	}
