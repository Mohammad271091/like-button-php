# like-button-php
facebook-like Like button with php, ajax, and mysql

|-------------------------------------------
|DEMO: http://fb-likes.epizy.com/
|===========================================


This is a sample code for a facebook-like Like button made with PHP [native], and Ajax to prevent page refresh with every request.
The page will show the last status of the like button and the number of clicks in real time (without refresh) but only from the local user.

if you want to see the last status of posts/likes with other users interactions, you will need to refresh. 
This is done only to make it run faster on the relatively slow shared hosting.
if you want to make it update without refresh after anyone's interaction, you can simply use ajax the same way, or make it send requests to the backend every (n) seconds to get the updated status.

used technologies/Libraries/Packages:

PHP (any version, locally v8.1, on the shared hosting v7.4) 
MySQL 
PhpMyAdmin
AJAX
jQuery
Carbon ( v2.61, but any version would work)
Bootstrap

** install Carbon on your host with composer, or download the standalone version if you don't have the possibility to install composer
