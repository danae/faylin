===================
Collection resource
===================

An collection represents a collection/album of images along with its associated metadata.


--------------------------------
Restrictions on collection names
--------------------------------

The following restrictions are enforced on collection names and titles:

- Collection names must be between 3 and 32 characters long and can only contain A-Z, a-z, 0-9, the hyphen and underscore.
- Collection titles (display names) must be between 1 and 64 characters long. They can contain most valid Unicode characters.


---------------------
The collection object
---------------------

A collection object has the following fields:

:id\: ``string``: The identifier of the collection.
:name\: ``string``: The name of the collection. Minimal 3 and maximal 32 characters long and can only contain ``[A-Za-z0-9-_]``.
:createdAt\: ``datetime``: The date the collection was created.
:updatedAt\: ``datetime``: The date the collection was last updated.
:user\: ``user``: The user that owns the collection.
:images\: ``array<image>``: An array of images that are contained in the collection.
:title\: ``string``: The title of the collection, which is used as the display name. Minimal 1 and maximal 64 characters long.
:description\: ``string``: The description of the collection. maximal 256 characters long.
:public\: ``boolean```: Indicates if the collection is listed publicly.


----------------
List collections
----------------

::

  GET /api/v1/collections/

Fetches all collections. The request can return one of the following status codes:

:200 OK: with an array of the fetched collections on success.
:401 Unauthorized: if the request doesn't contain authorization.
:422 Unprocessable Entity: if the query parameters are invalid.

Query parameters
----------------

:sort\: ``string?``: A comma-separated list of fields to sort the fetched collections by. optionally precided by a minus sign to indicate descending order. Defaults to ``"-createdAt"``.
:page\: ``integer?``: The page of the fetched collections to return, to use with pagination. This field must be used in conjunction with the perPage field. Defaults to ``0``.
:perPage\: ``integer?``: The number of fetched collections to return, to use with pagination. This field must be used in conjunction with the page field. Defaults to ``20``.


-----------------------------------------
List collections that are owned by a user
-----------------------------------------

::

  GET /api/v1/users/{user.id}/collections/

Fetches all collections that are owned by the user for the given id. The request can return one of the following status codes:

:200 OK: with an array of the fetched collections on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to fetch the collections.
:404 Not Found: if no user with the given identifier exists.
:422 Unprocessable Entity: if the query parameters are invalid.

Query parameters
----------------

The query parameters for this request are the same as the `GET /api/v1/collections/` endpoint.


------------------------------------------------------
List collections that are owned by the authorized user
------------------------------------------------------

::

  GET /api/v1/me/collections/

Fetches all collections that are owned by the user that is currently authorized. The request can return one of the following status codes:

:200 OK: with an array of the fetched collections on success.
:401 Unauthorized: if the request doesn't contain authorization.
:422 Unprocessable Entity: if the query parameters are invalid.

Query parameters
----------------

The query parameters for this request are the same as the `GET /api/v1/collections/` endpoint.


----------------
Get a collection
----------------

::

  GET /api/v1/collections/{collection.id}

Fetches the collection for the given identifier. The request can return one of the following status codes:

:200 OK: with the fetched collection object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to fetch the collection.
:404 Not Found: if no collection with the given identifier exists.


-------------------
Modify a collection
-------------------

::

  PATCH /api/v1/collections/{collection.id}

Modify the structure of a collection. The request can return one of the following status codes:

:200 OK: with the updated collection object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to modify the collection.
:404 Not Found: if no collection with the given identifier exists.
:422 Unprocessable Entity: if the body parameters are invalid.

Body parameters
---------------

:title\: ``string``: The title of the collection, which is used as the display name. Minimal 1 and maximal 64 characters long.
:description\: ``string``: The description of the collection. Maximal 256 characters long.
:public\: ``boolean``: Indicates if the collection is listed publicly.

If any of the body parameters is absent, then that field will not be updated in the collection object.


-------------------
Delete a collection
-------------------

::

  DELETE /api/v1/collections/{collection.id}

Delete a collection permanently.

.. warning::

   This action is irreversible!

The request can return one of the following status codes:

:204 No Content: on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to delete the collection.
:404 Not Found: if no collection with the given identifier exists.
