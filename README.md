# Usage Example and Guide

```php
// You can also choose not to send this in to use the config file but I want to support DI
$config = array(
	'key' 			=> 'YOUR_API_KEY',
	'blog' 			=> 'YUOR_BLOG_URL',
	'user_agent'	=> 'Kohana/3.x | Akismet/2.x',
    'server'    	=> 'rest.akismet.com',
    'port'      	=> 80,
);

/**
 * This is basically the core of everything. This call takes a number of
 * arguments and characteristics about the submitted content and then
 * returns a thumbs up or thumbs down. Almost everything is optional, but
 * performance can drop dramatically if you exclude certain elements. I
 * would recommend erring on the side of too much data, as everything is
 * used as part of the Akismet signature.
 *
 * blog (required)
 *  The front page or home URL of the instance making the request. For a
 *  blog or wiki this would be the front page. Note: Must be a full URI,
 *  including http://.
 * user_ip (required)
 *  IP address of the comment submitter.
 * user_agent (required)
 *  User agent information.
 * referrer (note spelling)
 *  The content of the HTTP_REFERER header should be sent here.
 * permalink
 *  The permanent location of the entry the comment was submitted to.
 * comment_type
 *  May be blank, comment, trackback, pingback, or a made up value like "registration".
 * comment_author
 *  Submitted name with the comment
 * comment_author_email
 *  Submitted email address
 * comment_author_url
 *  Commenter URL.
 * comment_content
 *  The content that was submitted.
 * Other server enviroment variables
 *  In PHP there is an array of enviroment variables called $_SERVER which
 *  contains information about the web server itself as well as a key/value
 *  for every HTTP header sent with the request. This data is highly useful
 *  to Akismet as how the submited content interacts with the server can be
 *  very telling, so please include as much information as possible.
 *
 * This call returns either "true" or "false" as the body content. True
 * means that the comment is spam and false means that it isn't spam. If you
 * are having trouble triggering you can send "viagra-test-123" as the
 * author and it will trigger a true response, always.
 *
 * @return  boolean
 */
$comment = array(
	'blog'    				=> 'http://your-blog-url.com',
	'user_ip'    			=> ($_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR')) ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR'),
	'user_agent'    		=> $_SERVER['HTTP_USER_AGENT'],
	'referrer'    			=> $_SERVER['HTTP_REFERER'],
	'permalink'    			=> 'http://your-blog-url.com/your-blog-post-url',
	'comment_type'    		=> 'comment',
	'comment_author'    	=> 'Author Name',
	'comment_author_email' 	=> 'author@example.com',
	'comment_author_url' 	=> 'http://url-submitted-by-author.com',
	'comment_content'   	=> 'Comment Content',
	'comment_content'   	=> 'Comment Content',
);

$akismet = Akismet::factory($config);

// Check if Spam
print_r($akismet->is_spam($comment)); // (bool) TRUE or FALSE;

// Mark as Spam
$akismet->submit_spam($comment);

// Mark as Ham
$akismet->submit_ham($comment);
```

