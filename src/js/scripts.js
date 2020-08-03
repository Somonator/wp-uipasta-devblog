(function ($) {
	$(document).ready(function () {
		// preloader
		$(window).load(function () {
			$('#preloader').delay(800).fadeOut('slow');
			$('body').removeClass('overflow-hidden');
		});

		// toggle main menu
		$('#main-menu').hide();
		$('#trigger-main-menu').click(function(event) {
			event.preventDefault();
			
			$('#main-menu').slideToggle(300);
		});

		$('a[href^="#"]').click(function(event) {
			if ($(this).attr('href') !== '#') {
				event.preventDefault();
			
				$('html, body').animate({
					scrollTop: $($.attr(this, 'href')).offset().top
				}, 500);
			}
		});

		// scroll to top
		$(window).scroll(function () {
			if ($(this).scrollTop() >= 500) {
				$('#scroll-to-top').fadeIn();
			} else {
				$('#scroll-to-top').fadeOut();
			}
		});

		$('#scroll-to-top').hide().click(function (event) {
			event.preventDefault();

			$('html, body').animate({
				scrollTop: 0
			}, 800);
		});

		// load more posts
		$('#load-more:not(.end)').click(function(){
			let self = $(this),
				self_text = self.text();

			if (!self.hasClass('loading')) {
				$.ajax({
					url: ajax_data.url,
					data: {
						'action': 'load_more',
						'query': ajax_data.query_vars,
						'page' : ajax_data.current_page
					},
					type:'POST',
					beforeSend: function() {
						self.addClass('loading');
						self.text(ajax_data.load_text);
					},
					success: function(data) {
						if (data) { 
							self.text(self_text).before(data).removeClass('loading');
							ajax_data.current_page++;

							if (ajax_data.current_page == ajax_data.max_pages) {
								self.addClass('end').text(ajax_data.end_while).off('click');
							}
						} else {
							self.remove();
						}
					},
					error: function() {
						alert(ajax_data.error_ajax);
					}
				});
			}
		});
	});
})(jQuery);