<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Configuration based on recommendations from https://docs.phpunit.de/en/11.4/installation.html#configuring-php-for-development

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors_max_len', 0);

ini_set('xdebug.show_exception_trace', 0);
ini_set('xdebug.mode', 'coverage');

// NOTE invokes a warning to enable in php.ini
// ini_set('zend.assertions', 1);

ini_set('assert.exception', 1);

// NOTE do not enable even though it's mentioned the configuration section. Dangerous setting.
// ini_set('memory_limit', 1);
