How to:
-------

Install Apache2 server, PostgreSQL, PHP5.5+

Clone or copy this repo to your server file location, e.g.: /var/www/html which is default in Ubuntu


Using API:
---------
Go to http://localhost and it should show a welcome page of Codeigniter, <b>assuming</b> you copied the repo to your server's root.


To explore the playlist suggestions and tag suggestions, please go to http://localhost/explore/YOUR_TAGS_HERE


In place of YOUR_TAGS_HERE, use tags that have been saved in the db. If a tag exists and it is mapped to a playlist, result will be shown.

To get a list of currently saved tags in DB, please use:
http://localhost/tag/tag_types?key=YOUR_KEY


For tags "punjabi","eminem","bhangra", use:
YOUR_TAGS_HERE = punjabi+eminem+bhangra

If a tag has more than one words, please use "_" in place of spaces:
For "AR Rehman", use:
YOUR_TAGS_HERE = ar_rehman


The response has playlists that are the best match, along with a few suggestions for tags.


Suggested tags:
--------------

Suggestions for tags can be greatly improved if checks for current playlists using the tag, total stats of the tag(play and like count for related playlists for a particular tag), and such data is generated and saved for use in suggestions.


Improving tag suggestions:
--------------------------

- Setting related tags for every tag in "tag" table can help narrow down which tags to prefer over others.
- Number of plays and likes associated to playlists mapped to a tag will also help
- 

CRUD:
-----
There are basically two types of CRUD APIs in the app:

- Tags:
GET, POST, DELETE, PUT are available on "localhost/tag?key=YOUR_KEY_HERE"


- Playlists:
GET, POST, DELETE, PUT work for playlists on "localhost/playlist?key=YOUR_KEY_HERE"


In both cases, GET and DELETE require a GET parameter passing "id" parameter.
Please use the Postman collections for details on this.



