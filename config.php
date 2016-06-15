<?php

return [
	'dsn' => '',				// Required, find it in your sentry app settings
	'dsnPublic' => '' 			// Required if you want to track front end (JS) errors
	'reportJsErrors' => 0 	    // Bool, enable front end error tracking
	'ignoredErrorCodes' => '', 	// Comma separated list of error codes to ignore, e.g. '404'
	'jsRegexFilter' => '', 		// If javascript error reporting is enabled, this will act as a case-insensitive filter to
								// only load raven-js on urls that match the expression. Can be any valid perl-style regexp
	'reportInDevMode' => 0, 	//Should the plugin report errors when in devMode?
];
