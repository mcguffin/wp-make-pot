POT Creator Script (†)
======================

**Deprecated!**  
Use `wp 118n make-pot` instead.


Installation:

    git clone git@github.com:mcguffin/wp-make-pot.git
	cd wp-make-pot
	git clone git@github.com:nikic/PHP-Parser.git

Usage:

    cd /path/to/my/theme/or/plugin
	/path/to/make-pot.php my-text-domain

Will create a pot file in `</path/to/my/theme/or/plugin/>languages/<my-text-domain>.pot`.  
Pot will contain only the strings with the specified textdomain.
Edit the POT header according to your needs. E.g. you might like to remove my name from the Copyright notice.

Contributing
------------
Pull requests are welcome.  
I will test and merge them if I think it makes sense.

Anything else will be sympathetically ignored.
