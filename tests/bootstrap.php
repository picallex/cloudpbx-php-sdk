<?php
// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>
declare(strict_types=1);

if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    exit(
        "\n[ERROR] You need to run composer before running the test suite.\n".
        "To do so run the following commands:\n".
        "    curl -s http://getcomposer.org/installer | php\n".
        "    php composer.phar install\n\n"
    );
}

require_once __DIR__.'/../vendor/autoload.php';

\VCR\VCR::configure()->setCassettePath('tests/cassettes');
\VCR\VCR::configure()->enableLibraryHooks(array('curl'));
\VCR\VCR::configure()
    ->enableRequestMatchers(array('method', 'url', 'host'));
\allejo\VCR\VCRCleaner::enable(array(
   'request' => array(
       'ignoreHostname' => false,
       'ignoreQueryFields' => array(
           'apiKey',
       ),
       'ignoreHeaders' => array(
           'x-api-Key',
       ))));
