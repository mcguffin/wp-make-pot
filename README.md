POT Creator Script
==================

Installation:

    git clone git@github.com:mcguffin/wp-make-pot.git
	cd wp-make-pot
	git clone git@github.com:nikic/PHP-Parser.git

Usage:

    cd /path/to/my/theme/or/plugin
	/path/to/make-pot.php my-text-domain

Will create a pot file in `</path/to/my/theme/or/plugin/>languages/<my-text-domain>.pot`.  
Pot will contain only the strings with the specified textdomain.

Contributing
------------
Pull requests are welcome.  
I will test and merge them if I think it makes sense.

Anything else will be sympathetically ignored.
