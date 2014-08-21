open-graph-control
==================

This is a simple singleton class that allows you to set custom fields on posts that fill in utm, open graph, and twitter parameters that can be wired into custom tweet buttons. This plugin differs from others in that it leaves actual implementation of the tweet button: styling, graphics, etc. to the theme.

Installation:
-------------

Like any WordPress plugin: download, upload to your server, activate.

Requirements:
-------------

Tested on WordPress version 3.8.3. No guarantees on versions earlier or newer, though unless WordPress core removes `the_title_attribute`, or the `plugins_loaded` and `wp_enqueue_scripts` action hooks, it should continue working.

This plugin uses PHP namespaces and will only work on systems with PHP 5.3 and later. Sorry, 5.2.x users, but you should really just upgrade already.

Facebook
--------

The facebook implementation is relatively straightforward. You have the option of wiring up two custom fields: `og_title` and `og_image` that, if set, are inserted into a `<meta>` tag. To make this more convenient, you may decide to add a custom meta box for this. That is not included here, for now.

UTM Data
---------

If you specify some utm-related custom fields these will be appended to any urls generated for the share buttons. Possible fields are:
- `utm_campaign`
- `utm_term`
- `utm_content`

The UTM variables 'utm_medium' and 'utm_source' are set dynamically. The former is set by the facebook and twitter methods respecitvely. The latter is set to either the UTM_SOURCE constant defined by you or to `bloginfo('url')`.

Twitter
-------
The tweet button is a bit lighter weight in that it doesn't reach back to the page for any data, it's just all in the URL parameters. Thus, the twitter url can be used by invoking the `tweet_url` method out of this class which will echo the full url for a tweet button. To set the 'via' URL parameter, define a constant called TWITTER_USER somewhere in your install (like wp-config.php with all the other constants). It also uses the following custom fields:
- `twtr_txt` for the `text` parameter, falls back on [`the_title_attribute`](http://codex.wordpress.org/Function_Reference/the_title_attribute) by default.
- `twtr_rel` for the `related` parameter, falls back to the defined user if empty
- `twtr_hash` for the `hashtags` parameter, does not set if empty
- `twtr_lang` if you want to set a different language from english

See [Twitter's documentation for the tweet button](https://dev.twitter.com/docs/tweet-button) for more information on what these parameters are and how to use them.