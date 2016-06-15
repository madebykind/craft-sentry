<?php

return array(
    'dsn' => !empty($_ENV['SENTRY_DSN']) ? $_ENV['SENTRY_DSN'] : '',
    'publicDsn' => !empty($_ENV['SENTRY_PUBLIC_DSN']) ? $_ENV['SENTRY_PUBLIC_DSN'] : '',
    'reportJsErrors' => 0,
    'ignoredErrorCodes' => '',
    'jsRegexFilter' => '',
    'reportInDevMode' => 0,
);
