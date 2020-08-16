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
		$('#load-more:not(.end)').click(function() {
			let self = $(this),
				self_text = self.text();

			if (!self.hasClass('loading')) {
				$.ajax({
					url: ajax_data.url,
					data: {
						'action': 'load_more',
						'query': load_more.query_vars,
						'page' : load_more.current_page
					},
					type:'POST',
					beforeSend: function() {
						self.addClass('loading');
						self.text(load_more.load_text);
					},
					success: function(data) {
						if (data) { 
							self.text(self_text).before(data).removeClass('loading');
							load_more.current_page++;

							if (load_more.current_page == load_more.max_pages) {
								self.addClass('end').text(load_more.end_while).off('click');
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

		/* subscribe with mailchimp */
		$('#subscribe-form').submit(function(event) {
			event.preventDefault();
			let form = $(this);

			$.ajax({
				url: ajax_data.url,
				data: {
					'action': 'subscribe_email',
					'email': form.find('[name="email"]').val(),
				},
				type:'POST',
				beforeSend: function() {
					$('#subscribe-notice').show().html('loading');
				},
				success: function(data) {
					if (data) { 
						$('#subscribe-notice').html(data);
						form.trigger('reset');
					}
				},
				error: function() {
					alert(ajax_data.error_ajax);
				}
			});
		});
	});
})(jQuery);