<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

    /**
     * Use of the Akismet API requires an API key, which are currently only
     * being provided along with accounts to WordPress.com.
     */
    'key' 			=> 'YOUR_API_KEY',
	'blog' 			=> URL::base(TRUE),

	'user_agent'	=> 'Kohana/'.Kohana::VERSION.' | Akismet/2.5.3',
    'server'    	=> 'rest.akismet.com',
    'port'      	=> 80,
);
