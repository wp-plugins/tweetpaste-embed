=== TweetPaste Embed ===
Contributors: moxypark
Tags: tweetpaste, twitter, tweet, embed, shortcode
Requires at least: 2.7
Tested up to: 2.8.4

Embed status updates from Twitter into blog posts and pages

== Description ==

This is a WordPress plugin created to allow users to paste tweets
directly into blog posts and pages.

It uses the [TweetPaste](http://tweetpaste.thingamaweb.com) API to
obtain a block, formatted in a similar way to twitter.com

== Installation ==

1. Upload `tweetpaste-embed` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Find the tweet you wish to embed, and click its permalink (the link
which gives the time at which the tweet was posted)
1. Copy the numerical ID from the last portion of the web address (for
example, the address http://twitter.com/tweetpaste/status/3689199935 gives
the ID for 3689199935)
1. Place `[tweet id=%id]` in a page, where %id is the numerical ID of the
tweet you wish to embed

You can place more options in the shortcode by looking at the Options page

== Options ==

All of the following are optional, and defaults can be customised through
the Settings > Embedded Tweets menu option in WordPress

= iframe_width =

The width (in pixels) of the tweet box (default is 500)

= iframe_height =

The height (in pixels) of the tweet box (default is 200).
Note that the box will scale vertically to fit the size of the tweet
(via JavaScript), so this setting isn’t always necessary

= show_background =

Valid values: `1` or `0`

Show the twitterer’s background colour (default is 1)

= include_padding =

Valid values: `1` or `0`

Useful if you’ve set show_background to 0; removes the padding around
the tweet box (default is 1)

= show_username =

Valid values: `1` or `0`

Show the twitterer’s username and avatar (default is 1)

= show_source =

Valid values: `1` or `0`

Show the source of the tweet: ie “via Web” (default is 1)