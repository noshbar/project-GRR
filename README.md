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
* Stores every news item permanently so you can search through your history, showing a link to a full-article local-copy if available
* If wkhtmltopdf is installed, you can save items to PDF from the main item page, otherwise the HTML page is downloaded using cURL or PHP get_contents
* Custom tags can be created and attached to posts, such as marking a post a "favourite", a C++ article "coding", etc.

#Features (TODO)

- [ ] Make all the new items searchable
- [ ] Add the ability to save the contents of an entry to disk (i.e., fetch the page the news item points to, along with all its resources and save it to disk)
- [ ] Add the ability to filter out posts containing keywords (e.g., "batman,spoilers")
- [ ] Be able to flag posts as important if they contain specified keywords (e.g., "chris brown,horrific accident")
- [ ] Be able to mail saved articles to people (mailto: Mr. Kindle)

#Bugs

- [ ] This currently looks like the enchanted upchuck of Satan himself. The idea is to get something working, then make it themeable or something.
- [X] If the update.php script runs while you're reading items the new-item count in your view won't update, so if you keep reading all the way up to the new items, the unread count will start going into the negatives
- [ ] The error-checking is abysmal if it's even present
- [X] Searching then selecting an article followed by clicking on a site from the site pane marks all items as read in the most horrific way possible.

#How to use

This is focussed on a "personal server" kind of scenario so far, but nothing should stop you from running it on a website that supports SQLite and cURL usage.

So install a webserver with PHP 5 and SQLite support, put all the files in a folder e.g., /var/www/arsse, and open a browser to e.g., http://localhost/arsse/index.html
OR
Get PHP 5.4 and run "php -S localhost:8080 -t ./codename-arsse", then browse to http://localhost:8080 in your browser.

To actually perform updates of content, have a cron job run update.php every oooh, 30 minutes or so? Or make it a link that you call on your webserver. It's just important that this script runs as often as your favourite websites update their feeds.

#Technologies used:

* SQLite (http://www.sqlite.org/)
* PHP (http://php.net)
* jQuery 1.9.1 (http://jquery.com/)
* jQuery Waypoints - v2.0.2 (https://github.com/imakewebthings/jquery-waypoints/blob/master/licenses.txt)
* RSS for PHP (http://phpfashion.com/)
* wkhtmltopdf (https://code.google.com/p/wkhtmltopdf/)
* idTabs (http://www.sunsean.com/idTabs/)