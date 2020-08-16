				</div>

				<? get_template_part('theme-parts/subscribe-form'); ?>
			</div>

			<footer class="footer block-appearance">
				<? wp_nav_menu([ 
					'container' => 'nav',
					'container_class' => 'footer-menu',
					'menu_class' => 'menu',
					'theme_location'  => 'footer-menu',
					'fallback_cb' => '__return_empty_string'
				]) ?>

				<p class="copyright"><? printf(__('© Copyright %1$s DevBlog. All rights reserved', 'upd'), date('Y')) ?></p>

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