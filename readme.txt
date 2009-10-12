=== TweetPaste Embed ===
Contributors: moxypark
Tags: tweetpaste, twitter, tweet, embed, shortcode
Requires at least: 2.7
Tested up to: 2.8.4
Stable tag: 1.0.0

Embed status updates from Twitter into blog posts and pages

== Description ==

This is a WordPress plugin created to allow users to paste tweets
directly into blog posts and pages.

It uses the [TweetPaste](http://tweetpaste.thingamaweb.com) API to
obtain a block, formatted in a similar way to twitter.com updates.

Tweets can be embedded using a simple shortcode, and there are a
number of options available to customise the look of the blocks.

== Installation ==

1. Upload `tweetpaste-embed` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Basic usage ==

1. Find the tweet you wish to embed, and click its permalink (the link
which gives the time at which the tweet was posted)
1. Note down the URL of the tweet
1. Place `[tweet url=%url]` in a page or post, where %url is
the permalink to the tweet.

You can also simply specify the ID of the tweet using `[tweet id=%id]`.
(The ID is the string of numbers at the end of the tweet permalink.)

== Customisation ==

All of the following are optional, and defaults can be customised through
the Settings > Embedded tweets menu option in WordPress

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