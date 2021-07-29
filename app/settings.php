<?php
use DI\ContainerBuilder;


// Return a function that adds settings to the container
return function(ContainerBuilder $containerBuilder)
{
  // Create the settings array
  $settings = [];

  // Include the default settings
  $settings['root'] = '/';

  $settings['secret'] = '';

  $settings['database.url'] = '';
  $settings['database.table.images'] = 'images';
  $settings['database.table.users'] = 'users';

  $settings['filesystem.adapter'] = null;

  $settings['snowflake.datacenter'] = 0;
  $settings['snowflake.worker'] = 0;
  $settings['snowflake.epoch'] = null;

  $settings['uploads.supportedContentTypes'] = [];
  $settings['uploads.supportedSize'] = 5242880; // 5 MiB


  // Include the environment settings
  if (file_exists(__DIR__ . '/../config/config.php'))
    require(__DIR__ . '/../config/config.php');

  // Add definitions to the container
  $containerBuilder->addDefinitions($settings);
};
