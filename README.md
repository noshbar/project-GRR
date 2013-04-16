project-GRR
===========

![logo](doc/grr.png "GRR!")  
Google Reader Replacement

#Features (so far)

* Allows adding of multiple sites
* Marks items as read once they leave the screen through scrolling
* Stores every news item permanently so you can search through your history, showing a link to a full-article local-copy if aavailable
* If wkhtmltopdf is installed, you can save items to PDF from the main item page, otherwise the HTML page is downloaded using cURL or PHP get_contents
* Custom tags can be created and attached to posts, such as marking a post a "favourite", a C++ article "coding", etc.

#Features (TODO)

- [ ] Add the ability to save the contents of an entry to disk (i.e., fetch the page the news item points to, along with all its resources and save it to disk)
- [ ] Add the ability to filter out posts containing keywords (e.g., "batman,spoilers")
- [ ] Be able to flag posts as important if they contain specified keywords (e.g., "chris brown,horrific accident")
- [ ] Be able to mail saved articles to people (mailto: Mr. Kindle)

#Bugs

- [ ] This currently looks like the enchanted upchuck of Satan himself. The idea is to get something working, then make it themeable or something.
- [ ] The error-checking is abysmal if it's even present

#How to use

This is focussed on a "personal server" kind of scenario so far, but nothing should stop you from running it on a website that supports SQLite and cURL usage.

So install a webserver with PHP 5 and SQLite support, put all the files in a folder e.g., `/var/www/arsse`, and open a browser to e.g., `http://localhost/project-GRR/index.html`  
OR  
Get PHP 5.4 and run `php -S localhost:8080 -t ./project-GRR`, then browse to `http://localhost:8080` in your browser.

To actually perform updates of content, have a cron job run update.php every oooh, 30 minutes or so? Or make it a link that you call on your webserver. It's just important that this script runs as often as your favourite websites update their feeds.

#Technologies used

* SQLite (http://www.sqlite.org/)
* PHP (http://php.net)
* jQuery 1.9.1 (http://jquery.com/)
* jQuery Waypoints - v2.0.2 (https://github.com/imakewebthings/jquery-waypoints/blob/master/licenses.txt)
* RSS for PHP (http://phpfashion.com/)
* wkhtmltopdf (https://code.google.com/p/wkhtmltopdf/)
* idTabs (http://www.sunsean.com/idTabs/)