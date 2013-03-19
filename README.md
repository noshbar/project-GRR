codename-aRSSe
==============

Advanced RSS Engine (Google-Reader-ish replacement)

#Introduction

Google Reader is going out of action on the 1st of June 2013.

This makes me sad.

So I'm going to try and make a clone of it that implements the functionality I use in Google Reader as well as add features that I think it should have had ages ago.

However, this is my first real attempt at using jQuery and HTML5 and the magic associated with it, so this really is just a hack for now.

#Features (so far)

* Allows adding of multiple sites
* Marks items as read once they leave the screen through scrolling
* Stores every news item permanently so you can search it later on when you think "Where did I see that posted again...?"

#Features (TODO)

- [ ] Add the ability to search through archived news items
- [ ] Make all the new items searchable
- [ ] Add the ability to save the contents of an entry to disk (i.e., fetch the page the news item points to, along with all its resources and save it to disk)
- [ ] Add the ability to filter out posts containing keywords (e.g., "batman,spoilers")
- [ ] Be able to flag posts as important if they contain specified keywords (e.g., "chris brown,horrific accident")

#Bugs

- [ ] This currently looks like the enchanted upchuck of Satan himself. The idea is to get something working, then make it themeable or something.
- [ ] If the update.php script runs while you're reading items the new-item count in your view won't update, so if you keep reading all the way up to the new items, the unread count will start going into the negatives
- [ ] The error-checking is abysmal if it's even present

#How to use

This is focussed on a "personal server" kind of scenario so far, but nothing should stop you from running it on a website that supports SQLite and cURL usage.

1. Have a cron job run update.php every oooh, 30 minutes or so? Or make it a link that you call on your webserver. It's just important that this script runs as often as your favourite websites update their feeds.
2. Open index.html in a browser and away you go!

#Technologies used:

* SQLite (http://www.sqlite.org/)
* PHP (http://php.net)
* jQuery 1.9.1 (http://jquery.com/)
* jQuery Waypoints - v2.0.2 (https://github.com/imakewebthings/jquery-waypoints/blob/master/licenses.txt)
* RSS for PHP (http://phpfashion.com/)