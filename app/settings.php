<?php
use DI\ContainerBuilder;


// Return a function that adds settings to the container
return function(ContainerBuilder $containerBuilder)
{
  // Create the settings array
  $settings = [];

  // Include the default settings
  $settings['app.basePath'] = '/';

  $settings['database.url'] = '';
  $settings['database.table.clients'] = 'clients';
  $settings['database.table.images'] = 'images';
  $settings['database.table.users'] = 'users';

  $settings['authorization.publicKey'] = '';
  $settings['authorization.privateKey'] = '';
  $settings['authorization.encryptionKey'] = '';
  $settings['authorization.accessTokenTTL'] = new DateInterval('PT1H');
  $settings['authorization.refreshTokenTTL'] = new DateInterval('P1D');

  $settings['filesystem.adapter'] = null;

  $settings['snowflake.datacenter'] = 0;
  $settings['snowflake.worker'] = 0;
  $settings['snowflake.epoch'] = 1288834974657;

  $settings['uploads.supportedContentTypes'] = [];
  $settings['uploads.supportedSize'] = 5242880; // 5 MiB


  // Include the environment settings
  if (file_exists(__DIR__ . '/../config/config.php'))
    require(__DIR__ . '/../config/config.php');

  // Add definitions to the container
  $containerBuilder->addDefinitions($settings);
};
