const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .addEntry('home', './assets/home.js')
    .addStyleEntry('css/app', './assets/styles/app.scss')
    .enableStimulusBridge('./assets/controllers.json')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    /* .configureBabel((config) => {
        config.plugins.push('@babel/a-babel-plugin');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    }) */
    .enableSassLoader()
    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    });

module.exports = Encore.getWebpackConfig();