<form role="search" method="get" class="search-form" action="<? echo home_url( '/' ); ?>">
    <input type="search" name="s" class="search-field" placeholder="<? echo esc_attr_x('Search …', 'placeholder', 'upd') ?>" value="<? echo get_search_query() ?>">
	<input type="submit" class="search-submit" value="<? echo esc_attr_x('Search', 'submit button', 'upd') ?>" />
</form>