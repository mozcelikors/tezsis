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
 
$(document).ready(function(){
var nesne_ismi=new Array("Tam Isim","Sifre","Sifre Tekrari","Email","Hakkinizda");
var nesneler=$('#fullName,#password,#password2,#Email,#About');						   
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
/*
$("#isim").keydown(function(e){
 if ((e.which>=48)&&(e.which<=57))
  e.preventDefault();
})

$("#telefon").keydown(function(e){
  if ((e.which>=48)&&(e.which<=57))
   return true;
  else
   e.preventDefault();
  })
*/
$("#form1").submit(function(){	 
var desen=/^\w+[\+\.\w-]*@([\w-]+\.)*\w+[\w-]*\.([a-z]{2,4}|\d+)$/i
var netice=desen.test($('#Email').val())
if (netice==false){
 $(".validationInfo:eq(3)").empty().addClass('redInfo').html('<br />Email hatali. Guvenliginiz icin sayfayi yenileyiniz.');
$('#mail').focus();
}
return netice;
})
});

