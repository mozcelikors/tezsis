/************************
 * TezSis: TezYonetimSistemi
 *
 * @brief Akademisyenler ve ögrenciler arasindaki arayüzü saglayarak tez islemlerini yürütmeyi saglayan PHP tabanli bir web sistemidir.
 * @version 1.1
 * @author Mustafa Özçelikörs <mozcelikors>
 * @contact mozcelikors@gmail.com
 * @website thewebblog.net
 ******/
 
function form_input_is_numeric(input){
    return !isNaN(input);
}

function validateUserLogin(){
	if(!document.form2.username.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form2.pass.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!form_input_is_numeric(document.form2.username.value)){
		alert("Ogrenci numaraniz harf iceremez. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	return true;
}

function validateRegisterUser(){
	// studentNumber 12 haneli sadece olabilir..
	if(document.form1.studentNumber.value.toString().length != 12){
		alert("Ogrenci numaraniz sadece 12 haneli olabilir. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	//Hicbir alan bos birakilmasin
	if(!document.form1.studentNumber.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.fullName.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.password.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.passwordRepeat.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	//Sifrelerin uyusup uyusmadigina bakalim
	if(document.form1.password.value != document.form1.passwordRepeat.value){
		alert("Girilen sifreler ayni degil. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.Email.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.About.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	// studentNumber sadece sayilardan olusabilir..
	if(!form_input_is_numeric(document.form1.studentNumber.value)){
		alert("Ogrenci numaraniz harf iceremez. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	return true;
}

$(document).ready(function(){
var nesne_ismi=new Array("Ogrenci Numarasi","Tam Isim","Sifre","Sifre Tekrari","Email","Hakkinizda");
var nesneler=$('#studentNumber,#fullName,#password,#passwordRepeat,#Email,#About');						   
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
 $(".validationInfo:eq(4)").empty().addClass('redInfo').html('<br />Email hatali. Guvenliginiz icin sayfayi yenileyiniz.');
$('#mail').focus();
}
return netice;
})
});
