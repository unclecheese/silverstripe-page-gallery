if(typeof(ss) == 'undefined' || typeof(ss.i18n) == 'undefined') {
    if(typeof(console) != 'undefined') console.error('Class ss.i18n not defined');
} else {
    ss.i18n.addDictionary('en', {
        'PageGallery.ALLOWED_HERE': 'Allowed here',
        'PageGallery.ALL_PAGE_TYPES': 'All page types'
    });
}