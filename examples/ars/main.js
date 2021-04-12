$(document).ready(function() {
	setTimeout(showReminder, 30000);	
	function showReminder(){
		// Проверим, есть ли запись в куках о посещении посетителя
		// Если запись есть - ничего не делаем
		if (!$.cookie('was')) {
			// Покажем всплывающее окно
			$('.autorisation_reminder').arcticmodal({
			  closeOnOverlayClick: true,
			  closeOnEsc: true
			});
		}
		// Запомним в куках, что посетитель к нам уже заходил
		$.cookie('was', true, {
			expires: 1,
		});	
	}
	setTimeout(function() { 
		var footerHeight = $('.footer_wrapper').height() + 40;
		$('.page_wrapper').css('padding-bottom', footerHeight);
		$('.footer_wrapper').css('margin-top', footerHeight*-1);
	}, 100);
	$(window).resize(function(){
		var footerHeight = $('.footer_wrapper').height() + 40;
		$('.page_wrapper').css('padding-bottom', footerHeight);
		$('.footer_wrapper').css('margin-top', footerHeight*-1);
	});
	$('.header_menu_btn, .close_header_menu, .header_menu_a_map').click(function(e){
		if($('.header_menu_ul').hasClass('opened')){
			$('.header_menu_ul').removeClass('opened');
			$('.profile_menu_mask').removeClass('opened');
		}else{
			$('.header_menu_ul').addClass('opened');
			$('.profile_menu_mask').addClass('opened');
			if($('.profile_menu_wrapper').hasClass('opened')){
				$('.profile_menu_wrapper').removeClass('opened')
				$('.user_wrapper').removeClass('opened')
			}
		}
	})
	if ($(window).width() > 992){
		if($("div").is("#particle-canvas1")){
			particlesJS("particle-canvas1", {"particles":{"number":{"value":110,"density":{"enable":true,"value_area":900}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":3},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":1,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":2,"random":true,"anim":{"enable":true,"speed":2,"size_min":0.1,"sync":true}},"line_linked":{"enable":true,"distance":127.82952832645452,"color":"#ffffff","opacity":1,"width":0.2},"move":{"enable":true,"speed":2,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":true,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"window","events":{"onhover":{"enable":true,"mode":"grab"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":487.24632738080703,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":false});
		}
	}
	function menuScroll(){
		var scroll = $(this).scrollTop();
	    var headerHeight = $('.header_wrapper').height();
	    var pageHeight = $('.page_wrapper').height();
	    var footerHeight = $('.footer_wrapper').height();
	    var menuHeight = $('.profile_menu_wrapper').height();
	    // console.log('headerHeight'+headerHeight+'pageHeight'+pageHeight+'footerHeight'+footerHeight+'scroll'+scroll);
	    if(scroll >= pageHeight-menuHeight-headerHeight){
	    	$('.profile_menu_wrapper').addClass('menu_bottom_fixed');
	    }
	    else{
	    	$('.profile_menu_wrapper').removeClass('menu_bottom_fixed');
	    }
	}
	$(window).scroll(function (e) {
	    menuScroll(e);
	});
	menuScroll();
	$('.user_a').click(function(e){
		if($('.profile_menu_wrapper').hasClass('opened')){
			$('.profile_menu_wrapper').removeClass('opened');
			$('.profile_menu_mask').removeClass('opened');
			$('.user_wrapper').removeClass('opened');
		}else{
			$('.profile_menu_wrapper').addClass('opened');
			$('.user_wrapper').addClass('opened');
			$('.profile_menu_mask').addClass('opened');
			if($('.header_menu_ul').hasClass('opened')){
				$('.header_menu_ul').removeClass('opened')
			}
		}
	})
	$('.profile_menu_mask').click(function(e){
		if($('.profile_menu_wrapper').hasClass('opened') || $('.header_menu_ul').hasClass('opened')){
			$('.profile_menu_wrapper').removeClass('opened');
			$('.profile_menu_mask').removeClass('opened');
			$('.user_wrapper').removeClass('opened');
			$('.header_menu_ul').removeClass('opened');
		}else{
			$('.profile_menu_wrapper').addClass('opened');
			$('.user_wrapper').addClass('opened');
			$('.profile_menu_mask').addClass('opened');
			$('.header_menu_ul').addClass('opened');
		}
	})
	/* Переключение регистрации и авторизации */
	$('.modal_tab_a, .small_modal_tab, .forgot_password_js').click(function(e){
		e.preventDefault();
		if($(this).hasClass('auth_tab_js')){
			$('.modal_tab_a').removeClass('selected');
			$(this).addClass('selected');
			$('.modal_auth_wrapper').css('opacity', '1');
			$('.modal_auth_wrapper').css('z-index', '2');
			$('.modal_reset_wrapper').css('opacity', '0');
			$('.modal_reset_wrapper').css('z-index', '1');
			$('.modal_registration_wrapper').css('opacity', '0');
			$('.modal_registration_wrapper').css('z-index', '1');
			$('.modal_form_wrapper').addClass('modal_form_auth');
			$('.modal_form_wrapper').removeClass('modal_form_reg');
			$('.modal_form_wrapper').removeClass('modal_form_reset');
		}
		else if($(this).hasClass('forgot_password_js')){
			$('.modal_auth_wrapper').css('opacity', '0');
			$('.modal_auth_wrapper').css('z-index', '1');
			$('.modal_registration_wrapper').css('opacity', '0');
			$('.modal_registration_wrapper').css('z-index', '1');
			$('.modal_reset_wrapper').css('opacity', '1');
			$('.modal_reset_wrapper').css('z-index', '2');
			$('.modal_form_wrapper').addClass('modal_form_reset');
			$('.modal_form_wrapper').removeClass('modal_form_reg');
			$('.modal_form_wrapper').removeClass('modal_form_auth');
		}
		else{
			$('.modal_tab_a').removeClass('selected');
			$(this).addClass('selected');
			$('.modal_auth_wrapper').css('opacity', '0');
			$('.modal_auth_wrapper').css('z-index', '1');
			$('.modal_reset_wrapper').css('opacity', '0');
			$('.modal_reset_wrapper').css('z-index', '1');
			$('.modal_registration_wrapper').css('opacity', '1');
			$('.modal_registration_wrapper').css('z-index', '2');
			$('.modal_form_wrapper').removeClass('modal_form_auth');
			$('.modal_form_wrapper').removeClass('modal_form_reset');
			$('.modal_form_wrapper').addClass('modal_form_reg');
		}
	})
	$('.header_menu_a_map').on('click', function(e){
	  	$('html,body').stop().animate({ scrollTop: $('#map').offset().top-$('.header_wrapper').height() }, 1000);
	  e.preventDefault();
	});
	window.hashName = window.location.hash;
  	window.location.hash = '';
  	if(hashName != ''){
	  	$(window).load(function () {
	      $('html, body').animate({scrollTop: $(window.hashName).offset().top-$('.header_wrapper').height()}, 1000);
	      return false;
	  	});	
  	}
  	
})