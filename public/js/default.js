// Define variables

// Environment
$.ajaxSetup ({
    cache: false
});

// Ready
jQuery(document).ready(function() {
	// Hide background if branding is run
	if($('.branding').length > 0){
		$('html').css('background', 'none');
	}

	// Datepicker
	$("#search form .date-time .popup .picker").datepicker({
		minDate: 0,
		maxDate: 7,
		dateFormat: 'yy-mm-dd',
		onSelect: function(dateText, inst) {
			var current_date = $("#search-date");
			current_date.val(dateText);
			$("#search form .date-time .popup").hide();
		}
	});
	
	// Time list
	$("#search form .category .list").each(function() {
		var list = $(this);
		var parent = $(this).parent().parent();
		var current_category = $("#search-category");
		
		// start up
		if (current_category.val() != '')
		{
			$("a", list).each(function() {
				if ($(this).attr('rel') == current_category.val())
				{
					$('a', list).removeClass('active');
					$(this).addClass('active');
					$(".input span", parent).html($(this).html());
				}
			});
		}
		
		// actions
		$('a', list).click(function() {
			$('a', list).removeClass('active');
			$(this).addClass('active');
			
			var selected_value = $(this).attr('rel');
			var selected_text = $(this).html();
			current_category.val(selected_value);
			
			$(".popup", parent).hide();
			$(".input span", parent).html(selected_text);
		});
	});
	
	// Time list and Date
	$("#search form .date-time .list").each(function() {
		var list = $(this);
		var parent = $(this).parent().parent();
		var current_time = $("#search-time");
		var current_date = $("#search-date");
		
		// start up
		if (current_time.val() != '')
		{
			$("a", list).each(function() {
				if ($(this).attr('rel') == current_time.val())
				{
					$('a', list).removeClass('active');
					$(this).addClass('active');
					$(".input span", parent).html($(this).html());
				}
			});
		}
		
		
		if (current_date.val() != '')
		{
			$('.popup .picker', parent).datepicker( "setDate" , current_date.val() );
		}
		
		
		// actions
		$('a', list).click(function() {
			$('a', list).removeClass('active');
			$(this).addClass('active');
			
			var selected_value = $(this).attr('rel');
			var selected_text = $(this).html();
			current_time.val(selected_value);
			
			$(".popup", parent).hide();
			$(".input span", parent).html(selected_text);
		});
	});
	
	// Search submit button
	$('#search ul li.btn a').click(function() {
            $('#search form').submit();
	});
	
	// Show category popup
	$('#search form .category .input, #search form .date-time .input').bind('click', function(){
		var parent = $(this).parent();
		var popup = $('.popup', parent);
		
		if (popup.css('display') == 'block')
		{
			$('#search form ul .popup').hide();
		}
		else
		{
			$('#search form ul .popup').hide();
			popup.show();
		}
	});
	
	// Accents
//    $('#accents ul').jcarousel({
//        scroll: 1,
//        animation: 'slow'
//    });


//	$("#accents .slider .image:eq(0)").addClass('active');
//	$("#accents .slider h3:eq(0)").addClass('active');
	

	$('#next').click(function(){
		var click = $('#accents ol li .active').parent().index()+1;
		var length = $("#accents ol li").length;
		int=self.clearInterval(int);
		$('a',$("#accents ol li a.active").parent().next()).mouseover();
	});
	
	$('#previus').click(function(){
		var click = $('#accents ol li .active').parent().index()+1;
		var length = $("#accents ol li").length;
		$('a',$("#accents ol li a.active").parent().prev()).mouseover();
		int=self.clearInterval(int);
	});

	$("#accents ol li a").mouseover(function(e){
		var search = $('#accents ol li a').index(this);
		
		if(search > 0){$('#previus').show();}
		if(search < 1){$('#previus').hide();}
		if(search == $("#accents ol li").length-1){$('#next').hide();}else{$('#next').show();}
		
		$("#accents ol li a").removeClass("active");
		$(this).addClass("active");
		
		if(e.clientX){
			int=self.clearInterval(int);
		}

		if(!$("#accents .slider .image:eq("+search+")").hasClass('active')){
			$("#accents .slider h3").removeClass("active");
			$("#accents .slider h3:eq("+search+")").addClass("active");
			
			$("#accents .slider .image").attr("style","");
			$("#accents .slider a.before").removeClass("before");
			$("#accents .slider a.active").addClass("before").removeClass("active");
			$("#accents .slider .image:eq("+search+")").addClass("active").hide().fadeIn("slow");
		}
	});
	 
	var int = setInterval('change_carousel();',3000);

    $('#article .gallery ul').jcarousel({
        scroll: 1,
        animation: 'slow'
    });
    
//	$('#accents ol li a').bind('click', function() {
//		$('#accents ol li a').removeClass('active');
//		$(this).addClass('active');
//		
//		var parent = $(this).parent();
//		var index = parent.index();
//		
//		$('#accents .slider').css('visibility', 'hidden');
//		$('#accents .slider:eq('+index+')').css('visibility', 'visible');
//	});
	
	// Accordion on articles
	$('#content .info-tabs .tab').bind('mouseover', accordion_articles);
	
	// TV 
	$('.tv-shows ul a').bind('mouseover', function() {
		var parent = $(this).parent();
		var master = parent.parent().parent();
		
		$('ul li', master).removeClass('active');
		$('ol', master).hide();
		
		parent.addClass('active');
		var index = parent.index();
		
		$('ol:eq('+index+')', master).show();
	});
	
	// Gallery effect
	if($('.gallery').length > 0){
		if($('#part_0').length == 0){
			if($('body.movies').length == 1 && $('.main-preview .player').length == 1){
				$('.gallery ul li img').click(function(){
					if($(this).parents('li').attr('id') == 'this_has_trailer'){
						$('.main-preview #p_icon').show();
					}else{
						$('.main-preview #p_icon').hide();
					}
					$('.main-preview .player').hide();
	   				$('.main-preview #wallpepar').show();
					$('.main-preview #wallpepar').find('img').attr('src', $(this).attr('src'));
					$('.main-preview #wallpepar').attr('href', $(this).parent('a').attr('href'));
				});
			}else{
				$('.gallery ul li img').mouseover(function(){
					$('.main-preview #wallpepar').find('img').attr('src', $(this).attr('src'));
					$('.main-preview #wallpepar').attr('href', $(this).parent('a').attr('href'));
				});				
			}
		}else{
			$('.gallery ul li img').mouseover(function(){				
				$('#'+ $(this).parent('a').attr('rel') +' .main-preview .wallpepar').find('img').attr('src', $(this).attr('src'));
				$('#'+ $(this).parent('a').attr('rel') +' .main-preview .wallpepar').attr('href', $(this).parent('a').attr('href'));
			});			
		}
	}
	
	// Observer
	if($('#container').length == 1 || $('.formbox').length == 1){
		observer();
	}
	
	// set .no-margin to the box
	if($('#home').length == 1){
		var position = 0;
		$('#home .box').each(function(){
			++position;
			
			if(position % 3 == 0){
				$(this).addClass('no-margin');
				$(this).after('<br class="clear"/>');
			}
		})
	}
	
	// set .active to first li in accents
	if($('#accents ol').length == 1){
		$('#accents ol li').first().find('a').addClass('active');
	}
	
	// login/profile button
	$('#login_profile_button').click(function(){
		if($(this).attr('class') != 'active'){
			$(this).addClass('active');
			$(this).css({
				'background-color': '#fff',
				'color': '#000',
				'border-right': 'solid 1px #e3e3e3'	
			})
			
			if($(this).attr('rel') == 'logged'){
				$('#login_form').addClass('logged')				
			}
		
			$('#login_form').css('display','block');				
		}else{
			$(this).removeClass('active');
			$(this).css({
				'background-color': '#000',
				'color': '#fff'
			})			
			// unactive
			if($(this).attr('rel') == 'logged'){
				$('#login_form').addClass('logged')				
			}
		
			$('#login_form').css('display','none');
		}
	});
	
	$('#snapshot').click(function(){
		$(this).hide();
		$('#hidden_panorama').show();
	});
	
	content_observer();
	
});

function change_carousel(){
	if($("#accents ol li a.active").parent().index()+1 == $("#accents ol li").length){
		$('a',$("#accents ol li:eq(0)")).mouseover();
	}else{
		$('a',$("#accents ol li a.active").parent().next()).mouseover();
	}
}
function accordion_articles(element)
{
	var el = element.currentTarget;
	var parent = $(el).parent();
	var master = parent.parent();
	
	var index = parent.index();
	
	$('.tab', master).unbind();
	
	$('.tab', master).show();
	$('.description', master).hide();
	
	$('.tab', parent).hide();
	$('.description', parent).show();
	setTimeout(function() {
		$('.tab', master).bind('mouseover', accordion_articles);
	}, 100);
}

function observer(){
	var min_height = 320; // default value in css
	var current_height = $('#menu .links').height();
	var distance = 0;

	if(current_height >= min_height){
		distance = current_height - min_height;

		if($('#container').length == 1 && $('.formbox').length == 0){
			// No animated
			$('#container').css('margin-top', (parseInt($('#container').css('margin-top')) - distance) +'px');
		}else{
			$('.formbox').css('margin-top', (parseInt($('.formbox').css('margin-top')) - distance) +'px');
			
			if($('#container').length == 1){
				$('#container').css('margin-top', '0px');	
			}
		}
		// Animated
//		$('#container').animate({
//			marginTop: (parseInt($('#container').css('margin-top')) - distance) +'px'
//		},'slow');
	}
}

function content_observer(){
	var content = $('#content');
	var container = $('#container');
	var sidebar = $('#search').height();
	var program = $('#search .box').height();
	
	
	if($('#search').length > 0){
		if(content.height() <= sidebar && container.length <= 0){
			content.css('height', (sidebar - program) +'px');
		}
		
		if(container.length > 0 && container.height() <= sidebar){
			content.css('height', (sidebar - program) +'px');			
		}
	}
	
//	setTimeout('content_observer();', 1000);
}