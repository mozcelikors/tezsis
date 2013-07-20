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
 
//-- jQuery Kontrolümüzü yapalim ----------
if( jQuery )

$(document).ready(function(){
// Açilir Menü Davranislari -------------------------------------
	// 2inci a elementini seçelim
	//----------------------------------------
	$("#nav_inner a:eq(0)").hover(uzerinde2,disinda2);
	function uzerinde2(){
		var x = $("#nav_inner a:eq(0)").offset().left;
		var y = $("#nav_inner a:eq(0)").offset().top;
		$("#popUpMenu").animate({
			left: x,
			top: y + 60
		},1);
		$("#popUpMenu").fadeIn(150);
	}
	function disinda2(){}
	
	

	//----------------------------------------
	$("#nav_inner a:eq(1),#nav_inner a:eq(2)").hover(uzerinde3,disinda3);
	
	function uzerinde3(){
		$("#popUpMenu").fadeOut(150);
	}
	function disinda3(){}
	

	
	//----------------------------------------
	$("#popUpMenu").mouseleave(function() {
		$(this).fadeOut(150);						
	});
	
	
	//------popUpMenu1 için düzenlemeler
	$("#popUpMenu").mouseenter(function(){
		$('#nav_inner a:eq(0)').css('background-image','url(images/navbg_hover7.png)');
		$('#nav_inner a:eq(0)').css('color','white');								
	});
	$("#popUpMenu").mouseleave(function(){
		$('#nav_inner a:eq(0)').css('background-image','url(images/navbg.png)');
		$('#nav_inner a:eq(0)').css('color','#666666');									
	});
	if(!$('#popUpMenu').is(':visible')) {
    	
	}
	//Istenen düzenleyici fonksiyon 
	$("#nav_inner a:eq(0)").hover(function(){
		$('#nav_inner a:eq(0)').css('background-image','url(images/navbg_hover7.png)');		
		$('#nav_inner a:eq(0)').css('color','white');	
	},function(){
		$('#nav_inner a:eq(0)').css('background-image','url(images/navbg.png)');
		$('#nav_inner a:eq(0)').css('color','#666666');	
	});
	

	
	
// Sayfa yeniden boyutlandirilinca --------------------------------------------
	$(window).resize(function(){
		
	});
	



//-- Submit butonu floodu engelleme ----------
	$("form").submit(function(){
		$('input[type=submit]',this).attr('disabled','disabled');				  
	});
	



		
})
