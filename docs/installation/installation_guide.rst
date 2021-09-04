==================
Installation guide
==================

To install a Faylin instance on your machine, execute the following steps:

1. Get the latest release from the GitHub repository at https://github.com/danae/faylin. Alternatively, clone the master branch using the following command:

::

  git clone https://github.com/danae/faylin.git

2. Install the dependencies for the backend using Composer:

::

  php composer.phar install

3. Install the dependencies for the frontend using NPM:

::

  cd public/
  npm install

4. Copy ``config/config.php.example`` to ``config/config.php`` and adjust the configuration variables to the ones appropriate for your instance.

5. That's all! Your Faylin instance should work out of the box now!
