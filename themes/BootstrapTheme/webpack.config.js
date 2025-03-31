const Encore = require('@symfony/webpack-encore');

Encore
  .setOutputPath('public/themes/bootstrap-theme')
  .setPublicPath('/themes/bootstrap-theme')
  .addEntry('app', './themes/BootstrapTheme/assets/index.js')
  .disableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableSassLoader()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction());

// Ajoutez une règle pour les fichiers images avec file-loader
Encore.addRule({
  test: /\.(png|jpe?g|gif|svg)$/i,
  use: [
    {
      loader: 'file-loader',
      options: {
        name: '[name].[hash:8].[ext]',
        outputPath: 'images/',  // Vous pouvez définir le chemin de sortie pour les images
        publicPath: '/themes/bootstrap-theme/images/',
      },
    },
  ],
});

const config = Encore.getWebpackConfig();
config.name = 'bootstrapTheme';

module.exports = config;
