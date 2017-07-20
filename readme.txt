=== CSFD Last Seen ===
Contributors: Josef Štěpánek
Donate link: http://josefstepanek.cz/kontakt
Tags: czech, česky, last seen, movie, CSFD, ČSFD, sidebar, widget, social, web 2.0
Requires at least: 2.2
Tested up to: 4.8
Stable tag: trunk

CSFD (ČSFD) Last Seen plugin adds a widget, which shows the last X movies rated on CSFD.cz (Czech-Slovak movie database).


== Description ==
CSFD (ČSFD) Last Seen plugin adds a widget, which shows the last X movies rated on CSFD.cz (Czech-Slovak movie database). It also displays link to the profile at the bottom.

Main features and notes:

* CSFD Last Seen sidebar widget displays the defined count of last seen movies on CSFD.cz. Just set is the link to user CSFD profile in the widget control panel and go.
* This plugin is in Czech language (as well as CSFD.cz)
* See <a href="http://wordpress.org/extend/plugins/csfd-last-seen/installation/">Installation</a> for setup info


Related Links:

* <a href="http://www.csfd.cz" target="_blank">CSFD.cz</a> (data source)
* <a href="http://josefstepanek.cz/835/wordpress-csfd-last-seen-plugin-0-9.html" title="Czech tutorial" target="_blank">Návod v češtině</a>


== Installation ==
1. Unzip and upload the `csfd-last-seen` directory to the `/wp-content/plugins/` directory.
2. Go to `WP Admin » Plugins` and activate the ‘CSFD Last Seen’ plugin
3. To display last seen movies from CSFD.cz in the sidebar, go to `WP Admin » Appearance » Widgets`, drag ‘CSFD Last Seen’ widget into the sidebar and don't forget to configure the link to user profile (i.e. https://www.csfd.cz/uzivatel/29153-joste/) and the count of movies to show.


== Frequently Asked Questions ==
No questions yet. Feel free to contact me at info@josefstepanek.cz


== Screenshots ==
1. Admin widget configuration
2. An example of use on a sidebar


==Version History==
* **2017-06-17: Version 1.8.2**
    * Minor code refactoring to fix some weird random bugs in the data table (fsockopen => curl)

* **2017-06-05: Version 1.8.1**
    * HTTPS redirect fix (caused no data error)

* **2016-04-09: Version 1.8**
    * Added HTTPS support, removed own star image in the settings

* **2016-04-09: Version 1.7**
    * Code and cache refactoring

* **2014-06-06: Version 1.6.1**
    * Change of some URLs on CSFD.cz

* **2012-06-01: Version 1.6**
    * A few minor fixes after the changes on CSFD.cz

* **2011-01-23: Version 1.5**
    * Changes to make this plugin prepared for the new version of CSFD.cz (now works with both – old and new)

* **2010-02-05: Version 1.4.1**
    * Major fix (during the CSFD.cz changes)

* **2009-12-27: Version 1.4**
    * Source code optimization, multiple widgets possible since now

* **2009-12-19: Version 1.3**
    * HTML => XHTML code fix + alt added to movie color

* **2009-12-13: Version 1.2**
    * Added many useful settings.

* **2009-12-06: Version 1.1**
    * Several bug fixes.

* **2009-12-06: Version 1.0**
    * First stable tested version.

* **2009-11-23: Version 0.9**
    * Initial release
