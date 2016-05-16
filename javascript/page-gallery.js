(function($) {
    $('#PageType ul li.disabled').entwine({
        onmatch: function () {
            if($('#PageType ul li.disabled').length && !$('.toggle-disabled-pagetypes').length) {
                var allCount = $('#PageType ul li').length;
                var allowedCount = allCount-$('#PageType ul li.disabled').length;
                $('<div class="toggle-disabled-pagetypes">'+
                    '<a data-toggle="allowed" href="#">' + ss.i18n._t('PageGallery.ALLOWED_HERE','Allowed here') + ' ('+allowedCount+')</a> | '+
                    '<a data-toggle="all" href="#">' + ss.i18n._t('PageGallery.ALL_PAGE_TYPES', 'All page types') + ' ('+allCount+')</a>'+
                  '</div>'
                ).insertBefore('#PageType ul');
                $('.cms-add-form').css({display: 'block'});
                $('.toggle-disabled-pagetypes a:first').click();
            }
        }
    });

    $('.toggle-disabled-pagetypes a').entwine({
        onclick: function (e) {
            e.preventDefault();
            $('.toggle-disabled-pagetypes a').removeClass('active');
            if(this.data('toggle') === 'allowed') {
                $('#PageType ul li.disabled').css({display: 'none'});
            }
            else {
                $('#PageType ul li.disabled').css({display: 'inline-block'});
            }
            this.addClass('active');
        }
    });

})(jQuery);