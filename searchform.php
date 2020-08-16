<form role="search" method="get" class="search-form" action="<? echo home_url( '/' ); ?>">
    <input type="search" name="s" class="search-field" placeholder="<? _e('Search…', 'upd') ?>" value="<? echo get_search_query() ?>">
	<input type="submit" class="search-submit" value="<? _e('Search', 'upd') ?>" />
</form>