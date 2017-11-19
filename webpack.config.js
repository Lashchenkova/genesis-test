let webpack = require('webpack');

module.exports = {
    context : __dirname + '/resources/public/js',
    entry   : {
        app     : __dirname + '/resources/public/js/main.js',
        vendor : ['underscore', 'jp-router']
    },
    output  : {
        path      : __dirname + '/web/assets/',
        publicPath: '/assets/',
        filename  : '[name].min.js',
        chunkFilename: '[id].[name].min.js'
    },
    plugins: [
        new webpack.optimize.CommonsChunkPlugin('vendor', '[name].min.js')
    ],
    module: {
        loaders: [
            {
                test: /\.js$/,
                loader: "babel",
                query:{presets:['es2015']},
                exclude:/node_modules/
            },
            {
                test: /\.vue$/,
                loader: 'vue'
            }
        ]
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.js'
        }
    }
};