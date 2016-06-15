<?php

namespace Craft;

/**
 * Sentry Variable.
 *
 * Lets you use Sentry in templates
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @author    Adam Burton <adam@burt0n.net>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/adamdburton/craft-sentry
 */
class SentryVariable
{
    /**
     * Returns Sentry DSN.
     *
     * @return string
     */
    public function dsn()
    {
        return craft()->config->get('dsn', 'sentry');
    }

    /**
     * Returns Sentry public DSN.
     *
     * @return string
     */
    public function publicDsn()
    {
        return craft()->config->get('publicDsn', 'sentry');
    }

    /**
     * Returns Reporting in devMode.
     *
     * @return string
     */
    public function reportInDevMode()
    {
        return $this->_settings->getAttribute('reportInDevMode');
    }

}
