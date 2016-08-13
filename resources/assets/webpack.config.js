/**
 * Created by xandros15 on 2016-08-11.
 */
var ExtractTextPlugin = require("extract-text-webpack-plugin");
var webpack = require('webpack');
module.exports = {
    entry: {
        main: [
            './js/blockDisabledLinks.js',
            './js/ajax.js',
            './js/openImages.js',
            './css/main.css',
            './js/popover.js',
            'bootstrap-webpack!./bootstrap.config.js',
        ],
        form: ['bootstrap-validator']
    },
    output: {
        path: __dirname + '/../../web/',
        filename: './js/[name].js',
        chunkFilename: './js/[id].js'
    },
    module: {
        loaders: [
            {test: /\.css$/, loader: ExtractTextPlugin.extract("style-loader", "css-loader")},
            {
                test: /\.(woff|woff2)$/,
                loader: "url-loader?name=/fonts/[hash].[ext]&limit=10000&mimetype=application/font-woff"
            },
            {test: /\.ttf$/, loader: "file-loader?name=/fonts/[hash].[ext]"},
            {test: /\.eot$/, loader: "file-loader?name=/fonts/[hash].[ext]"},
            {test: /\.svg$/, loader: "file-loader?name=/fonts/[hash].[ext]"}
        ]
    },
    plugins: [
        new ExtractTextPlugin('[id]', '/css/[name].css'),
        new webpack.ProvidePlugin({$: "jquery", jQuery: "jquery"})
    ]
};