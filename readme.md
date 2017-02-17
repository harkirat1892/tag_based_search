Basic Codeigniter based RESTful server for working on tag based search, using Junction table in RDBMS.

Demo for an online music streaming company which has playlists and tags descibing the playlists.
As the relationship between playlists and tags is many-to-many, and because this demo is using RDBMS (PostgreSQL), a junction table is there to help keep the relations normalised.


Via REST API, CRUD operations can be performed on Tags and Playlists as per the need, with a GET based key authentication.

The app's primary purpose is to suggest relevant playlists to a user requesting playlists based on certain tags, available in "tag" table.

Better description of working will be written in another document in the same folder.

Tech Used
----------

- PHP 5.6 with Codeigniter 3 based on REST library (https://github.com/chriskacerguis/codeigniter-restserver)

- PostgreSQL 9.7

- Apache2, Linux


High Scalability:
----------------

For high scalability, we can use <b>Redis as a cache</b> to keep results for quick retrieval.

For most frequently asked queries, we can cache the results for 5-10mins, which will give a good boost to efficiency of the system.


<b>MongoDB</b> can also be seen as a replacement of PostgreSQL here. MongoDB has better read efficiency than RDBMS based DBs in case of Many-to-Many relations, hence better throughput.