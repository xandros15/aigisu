/**
 * Created by xandros15 on 2016-08-11.
 */
var debug = process.env.NODE_ENV !== "production";
var webpack = require('webpack');
var ExtractTextPlugin = require("extract-text-webpack-plugin");
var path = require('path');

module.exports = {
    entry: ['babel-polyfill', './js/main.js'],
    output: {
        path: path.resolve(process.cwd(), '../web/dist/'),
        publicPath: '/dist/',
        filename: 'main.js'
    },
    module: {
        loaders: [
            {
                test: /\.jsx?$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
                query: {
                    presets: ['react', 'es2015', 'stage-0'],
                    plugins: ['react-html-attrs', 'transform-decorators-legacy', 'transform-class-properties']
                }
            },
            {
                test: /\.less$/,
                loader: ExtractTextPlugin.extract('style',
                    'css?importLoaders=2&browsers=last 2 version!less?outputStyle=expanded&sourceMap=true&sourceMapContents=true'
                )
            },
            {
                test: /\.scss$/,
                loader: ExtractTextPlugin.extract('style',
                    'css?importLoaders=2&browsers=last 2 version!sass?outputStyle=expanded&sourceMap=true&sourceMapContents=true'
                )
            },
            {
                test: /\.json$/,
                loader: 'json-loader',
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2)$/,
                loader: 'file-loader',
            },
            {
                test: /\.html$/,
                loader: 'html-loader',
            },
            {
                test: /\.(jpg|png|gif)$/,
                loaders: [
                    'file-loader',
                    'image-webpack?{progressive:true, optimizationLevel: 7, interlaced: false, pngquant:{quality: "65-90", speed: 4}}',
                ],
            },
        ]
    },
    progress: true,
    resolve: {
        modulesDirectories: ['js', 'node_modules'],
        extensions: ['', '.js', '.jsx']
    },
    plugins: [
        new ExtractTextPlugin('style.css'),
    ].concat(debug ? [] : [
        new webpack.optimize.DedupePlugin(),
        new webpack.optimize.OccurenceOrderPlugin(),
        new webpack.optimize.UglifyJsPlugin({mangle: false, sourcemap: false}),
    ]),
    target: 'web', // Make web variables accessible to webpack, e.g. window
    devtool: debug ? "inline-sourcemap" : null,
};