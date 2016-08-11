/**
 * Created by xandros15 on 2016-08-11.
 */
module.exports = {
    styleLoader: require('extract-text-webpack-plugin').extract('style-loader', 'css-loader!less-loader'),
    scripts: {
        'transition': true,
        'alert': true,
        'button': true,
        'collapse': true,
        'modal': true,
        'popover': true,
        'tooltip': true
    },
    styles: {
        'mixins': true,
        'normalize': true,
        'print': true,
        'scaffolding': true,
        'type': true,
        'code': true,
        'grid': true,
        'tables': true,
        'forms': true,
        'glyphicons': true,
        'buttons': true,
        'button-groups': true,
        'input-groups': true,
        'navs': true,
        'navbar': true,
        'pagination': true,
        'pager': true,
        'labels': true,
        'badges': true,
        'thumbnails': true,
        'alerts': true,
        'media': true,
        'list-group': true,
        'panels': true,
        'close': true,
        'modals': true,
        'tooltip': true,
        'popovers': true,
        'utilities': true,
        'responsive-utilities': true
    }
};