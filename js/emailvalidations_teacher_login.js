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
 
function validateTeacherLogin(){
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
	return true;
}

function validateTeacherRegister(){
	if(!document.form1.registerPassword.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.teacherFullname.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.teacherName.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.teacherPassword.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.teacherPassword2.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(document.form1.teacherPassword.value != document.form1.teacherPassword2.value){
		alert("Girdiginiz sifreler birbirini tutmuyor. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.teacherEmail.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	if(!document.form1.teacherNotes.value){
		alert("Hicbir alani bos birakamazsiniz. Sayfa yenileniyor..");
		window.location.reload();
		return false;
	}
	return true;
}

$(document).ready(function(){
var nesne_ismi=new Array("Kayit Sifresi","Unvan/Isim","Kullanici Adi","Sifre","Sifre Tekrari","Email","Notlar");
var nesneler=$('#registerPassword,#teacherFullname,#teacherName,#teacherPassword,#teacherPassword2,#teacherEmail,#teacherNotes');						   
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
var netice=desen.test($('#teacherEmail').val())
if (netice==false){
 $(".validationInfo:eq(5)").empty().addClass('redInfo').html('<br />Email hatali. Guvenliginiz icin sayfayi yenileyiniz.');
$('#mail').focus();
}
return netice;
})
});