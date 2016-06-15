# Sentry plugin for Craft CMS

A Craft plugin that allows you to log Craft Errors/Exceptions to Sentry,

- Forked from [https://github.com/adamdburton/craft-sentry](https://github.com/adamdburton/craft-sentry)
- which is itself based on [https://github.com/boboldehampsink/rollbar](https://github.com/boboldehampsink/rollbar)

## Differences in this fork from [adamdburton/craft-sentry](https://github.com/adamdburton/craft-sentry)

- Requires PHP5.4+, because it's not 2006 any more and I can't stand `array()`.
- All settings are handled via config file ('craft/config/sentry.php'), bringing the benefit multi-enviroment support.
- Registers via `Raven_Client->install()` to log unhandled PHP errors and exceptions as well as Craft errors / exceptions
 
## Features

- Log Craft Errors/Exceptions to Sentry
- Logs the environment you're working on
- 2 min install
- You can limit what pages get JS Raven loaded on them by applying a page filter.
- You can limit which HTTP error codes get reported to sentry in the settings (for example, if you don't want to report 400 or 404 errors).

## Setup

Copy the config file from `sentry/config.php` to your config directory (normally `craft/config/sentry.php`)

```
cp sentry/config.php craft/config/sentry.php
```

Edit the config file to add your client keys from Sentry. There is only one required config setting (`dsn`).

```
'dsn' => '',				// Required, find it in your sentry app settings
'dsnPublic' => '' 			// Required if you want to track front end (JS) errors
'reportJsErrors' => 0 	    // Bool, enable front end error tracking
'ignoredErrorCodes' => '', 	// Comma separated list of error codes to ignore, e.g. '404'
'jsRegexFilter' => '', 		// If javascript error reporting is enabled, this will act as a case-insensitive filter to
							// only load raven-js on urls that match the expression. Can be any valid perl-style regexp
'reportInDevMode' => 0, 	//Should the plugin report errors when in devMode?
```
