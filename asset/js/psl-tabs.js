(function($) {
    $.fn.pslTabs = function() {
        this.each(function() {
            var container = $(this);
            container.children('ul').find('a').on('click', function(e) {
                e.preventDefault();
                container.children('div').hide();
                container.children('div' + $(this).attr('href')).show();
                container.find('.psl-tab-active').removeClass('psl-tab-active');
                $(this).parents('li').first().addClass('psl-tab-active');
            });

            container.children('ul').find('a').first().click();
        });
    };
})(jQuery);
