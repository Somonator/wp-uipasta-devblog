				</div>

				<!-- <div class="subscribe">
					<form action="#" class="subscribe-form">
						<input type="email" placeholder="Email Address" name="email" class="email">
						<input type="submit" class="submit" value="Submit">
					</form>

					<p><? _e('Subscribe to my weekly newsletter', 'upd') ?></p>

					<p id="notice" style="display: none;"><? _e('Submiting...', 'upd') ?></p>
				</div> -->
			</div>

			<footer class="footer block-appearance">
				<? wp_nav_menu([ 
					'container' => 'nav',
					'container_class' => 'footer-menu',
					'menu_class' => 'menu',
					'theme_location'  => 'footer-menu',
					'fallback_cb' => '__return_empty_string'
				]) ?>

				<p class="copyright"><? echo __('© Copyright', 'upd') . ' ' . date('Y') . ' ' . __('DevBlog. All rights reserved', 'upd') ?></p>

				<div class="uipasta-credit"><? _e('Design By', 'upd') ?> <a href="https://www.uipasta.com" target="_blank">UiPasta</a></div>
			</footer>
		</main>     
	</div>

	<a href="#" id="scroll-to-top" class="scroll-to-top">
		<i class="fa fa-long-arrow-up"></i>
	</a>

	<? wp_footer() ?>
</body>
</html>