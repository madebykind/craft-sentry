<?php

namespace Craft;

use Raven_Client;

/**
 * Sentry Plugin.
 *
 * Integrates Rollbar into Craft
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @author    Adam Burton <adam@burt0n.net>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/adamdburton/craft-sentry
 */
class SentryPlugin extends BasePlugin
{
	/**
	 * Get plugin name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return Craft::t('Sentry');
	}

	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return '1.1.1';
	}

	/**
	 * Get plugin developer.
	 *
	 * @return string
	 */
	public function getDeveloper()
	{
		return 'Adam Burton / Bob Olde Hampsink';
	}

	/**
	 * Get plugin developer url.
	 *
	 * @return string
	 */
	public function getDeveloperUrl()
	{
		return 'http://github.com/adamdburton';
	}

	/**
	 * Initialize Sentry.
	 */
	public function init()
	{
		try {

			// See if we have to report in devMode
			if (craft()->config->get('devMode')) {
				if (!craft()->config->get('reportJsErrors', 'sentry')) {
					return;
				}
			}

			$this->configureBackendSentry();

			$this->configureFrontendSentry();

		} catch (Exception $e) {
			SentryPlugin::log("Sentry encountered an error when trying to initialize: {$e}", LogLevel::Error);
		}
	}

	/**
	 * Hook into the errors and exceptions for the server
	 * side if we should be
	 * @return $this;
	 */
	protected function configureBackendSentry()
	{
		// Require Sentry vendor code
		require_once CRAFT_PLUGINS_PATH.'sentry/vendor/autoload.php';

		$dsn = craft()->config->get('dsn', 'sentry');
		// Initialize Sentry
		$client = new Raven_Client($dsn);
		$client->tags_context(array('environment' => CRAFT_ENVIRONMENT));
		$client->install();
		$this->attachRavenErrorHandlers($client);

		return $this;
	}

	/**
	 * Attaches the error and exception handlers
	 * for Sentry using the given client.
	 * @param  Raven_Client $client   Client to send the exceptions/messages to.
	 */
	protected function attachRavenErrorHandlers(Raven_Client $client)
	{
		// Log Craft Exceptions to Sentry
		craft()->onException = function ($event) use ($client) {
			if (!$this->shouldIgnoreException($event->exception)) {
				$client->captureException($event->exception);
			}
		};

		// Log Craft Errors to Sentry
		craft()->onError = function ($event) use ($client) {
			$client->captureMessage($event->message);
		};

		return $this;
	}

	/**
	 * Checks to see if the given HTTP exception should not be sent to Sentry
	 * For example, we may not want to send 404s to sentry.
	 * @param  \CHttpException $exception
	 * @return boolean True if we should ignore the xception and not send it to Sentry
	 */
	protected function shouldIgnoreException($exception)
	{
		if ($exception instanceof \CHttpException) {
			$ignoredCodes = explode(',', craft()->config->get('ignoredErrorCodes', 'sentry'));
			foreach ($ignoredCodes as $ignoredCode) {
				if ($exception->statusCode == intval($ignoredCode)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Includes the JS for front-end sentry.
	 * @return $this
	 */
	protected function configureFrontendSentry()
	{

		if (!$this->isWebRequest()) {
			return $this;
		}

		if (!craft()->config->get('reportJsErrors', 'sentry')) {
			return $this;
		}

		if (!$this->matchesJsFilter()) {
			return $this;
		}

		$publicDsn = craft()->config->get('publicDsn', 'sentry');

		if (empty($publicDsn)) {
			return $this;
		}

		craft()->templates->includeJsFile('//cdn.ravenjs.com/3.0.5/raven.min.js');

		craft()->templates->includeJs("Raven.config('{$publicDsn}').install()");

		return $this;
	}

	/**
	 * Checks to see if we should be including the Raven JS
	 * based on the request URI filter (if specified)
	 * @return boolean  True if we should include the JS, false otherwise.
	 */
	protected function matchesJsFilter()
	{

		$jsRegexFilter = craft()->config->get('jsRegexFilter', 'sentry');

		// If no filter specified, show JS on every page.
		if (empty($jsRegexFilter)) {
			return true;
		}
		$uri = craft()->request->getRequestUri();

		// Match using Regex
		$matchResult = @preg_match($jsRegexFilter, $uri);

		if ($matchResult === false) {
			// Filter is not valid regex, so match using a simple filter.
			return stripos($uri, $filter) !== false;
		}

		return ($matchResult === 1);
	}

	/**
	 *
	 * @return boolean true if this is a web control panel request and the user is currently logged in.
	 */
	protected function isWebRequest()
	{
		if (craft()->isConsole()){
			return false;
		}
		return true;
	}

}
