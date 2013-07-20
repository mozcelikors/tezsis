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
 
	function validateSearchQueryString(){
		if(!document.formSearch.q.value){
			alert("Bu alani bos birakamazsiniz. Guvenliginiz icin sayfayi yenilemeniz gerekmektedir.");
			return false;
		}
		return true;
	}
