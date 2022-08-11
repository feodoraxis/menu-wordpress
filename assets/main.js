jQuery(function ($) {
    let active_class = 'is-active';

    $('a[data-category]').on('mouseover', function() {
        let id = $(this).attr('data-category');

        if ( !$(id).hasClass( active_class ) ) {
            $('.catalog_menu-category').removeClass( active_class );
            $(id).addClass( active_class );
        }
    }); 
});