/************************
 * TezSis: TezYonetimSistemi
 *
 * @brief Akademisyenler ve �grenciler arasindaki aray�z� saglayarak tez islemlerini y�r�tmeyi saglayan PHP tabanli bir web sistemidir.
 * @version 1.1
 * @author Mustafa �z�elik�rs <mozcelikors>
 * @contact mozcelikors@gmail.com
 * @website thewebblog.net
 ******/
 
$(document).ready(function(){
var nesne_ismi=new Array("Sifre","Tez Konusu","Tez Kriterleri");
var nesneler=$('#password,#thesisTopic,#thesisCriteria');						   
nesneler.blur(function(){
	if ($(this).val()=="")
	{
	var t=nesneler.index($(this));
$(".validationInfo")
.eq(t).empty().addClass('redInfo')
.html('<br />' +nesne_ismi[t]+' alanini lutfen bos birakmayiniz.');
	}
	else
	{
	var t=nesneler.index($(this));
	$(".validationInfo").eq(t).empty().addClass('redInfo');
	}
	})
});

