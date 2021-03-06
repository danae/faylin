<?php
use DI\ContainerBuilder;


// Return a function that adds settings to the container
return function(ContainerBuilder $containerBuilder)
{
  // Create the settings array
  $settings = [];

  // Set default app settings
  $settings['app.basePath'] = '/';
  $settings['app.supportedContentTypes'] = [];
  $settings['app.supportedSize'] = 5242880; // 5 MiB

  // Set default database settings
  $settings['mongodb.uri'] = 'mongodb://localhost:27017';
  $settings['mongodb.database'] = 'faylin';
  $settings['mongodb.collection.collections'] = 'collections';
  $settings['mongodb.collection.images'] = 'images';
  $settings['mongodb.collection.sessions'] = 'sessions';
  $settings['mongodb.collection.users'] = 'users';

  // Set default store settings
  $settings['store.adapter'] = null;
  $settings['store.imageFileNameFormat'] = 'uploads/%s.gz';
  $settings['store.imageTransformCacheFileNameFormat'] = 'transforms/%s.gz';

  // Set default authorization settings
  $settings['authorization.signKey'] = '';

  $settings['snowflake.datacenter'] = 0;
  $settings['snowflake.worker'] = 0;
  $settings['snowflake.epoch'] = 1288834974657;


  // Include the settings from the configuration file
  if (file_exists(__DIR__ . '/../config/config.php'))
    require(__DIR__ . '/../config/config.php');

  // Add definitions to the container
  $containerBuilder->addDefinitions($settings);
};
