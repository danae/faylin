=============
User resource
=============

A user represents a user account along with its associated metadata. Users can own images and other resources.


--------------------------
Restrictions on user names
--------------------------

The following restrictions are enforced on user names and titles:

- User names must be between 3 and 32 characters long and can only contain A-Z, a-z, 0-9, the hyphen and underscore.
- User titles (display names) must be between 1 and 64 characters long. They can contain most valid Unicode characters.


---------------
The user object
---------------

A user object has the following fields:

:id\: ``string``: The identifier of the user.
:name\: ``string``: The name of the user. Minimal 3 and maximal 32 characters long and can only contain ``[A-Za-z0-9-_]``.
:createdAt\: ``datetime``: The date the user was created.
:updatedAt\: ``datetime``: The date the user was last updated.
:title\: ``string``: The title of the user, which is used as the display name. Minimal 1 and maximal 64 characters long.
:description\: ``string``: The description of the user. Maximal 256 characters long.
:public\: ``boolean``: Indicates if the user is listed publicly.


----------
List users
----------

::

  GET /api/v1/users/

Fetches all users. The request can return one of the following status codes:

:200 OK: with an array of the fetched users objects on success.
:401 Unauthorized: if the request doesn't contain authorization.
:422 Unprocessable Entity: if the query parameters are invalid.

Query parameters
----------------

:sort\: ``string?``: A comma-separated list of fields to sort the fetched users by. optionally precided by a minus sign to indicate descending order. Defaults to ``"-createdAt"``.
:page\: ``integer?``: The page of the fetched users to return, to use with pagination. This field must be used in conjunction with the perPage field. Defaults to ``0``.
:perPage\: ``integer?``: The number of fetched users to return, to use with pagination. This field must be used in conjunction with the page field. Defaults to ``20``.


----------
Get a user
----------

::

  GET /api/v1/users/{user.id}

Fetches the user for the given identifier. The request can return one of the following status codes:

:200 OK: with the fetched user object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to fetch the user.
:404 Not Found: if no user with the given identifier exists.


-------------
Modify a user
-------------

::

  PATCH /api/v1/users/{user.id}

Modify the structure of a user and save it to the database. The request can return one of the following status codes:

:200 OK: with the updated user object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to modify the user.
:404 Not Found: if no user with the given identifier exists.
:422 Unprocessable Entity: if the body parameters are invalid.

Body parameters
---------------

:name\: ``string``: The name of the user. Minimal 3 and maximal 32 characters long and can only contain ``[A-Za-z0-9-_]``.
:title\: ``string``: The title of the user, which is used as the display name. Minimal 1 and maximal 64 characters long.
:description\: ``string``: The description of the user. Maximal 256 characters long.
:public\: ``boolean``: Indicates if the user is listed publicly.

If any of the body parameters is absent, then that field will not be updated in the user object.


-----------------------
Get the authorized user
-----------------------

::

  GET /api/v1/me

Fetches the user that is currently authorized. This endpoint behaves exactly like the ``GET /api/v1/users/{user.id}`` endpoint.


--------------------------
Modify the authorized user
--------------------------

::

  PATCH /api/v1/me

Modify the structure of the user that is currently authorized. This endpoint behaves exactly like the ``PATCH /api/v1/users/{user.id}`` endpoint.


-----------------------------------------------
Update the email address of the authorized user
-----------------------------------------------

::

  POST /api/v1/me/email

Modify the email address of the user that is currently authorized. The request can return one of the following status codes:

:200 OK: with the updated user object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:422 Unprocessable Entity: if the body parameters are invalid.

Body parameters
---------------

:email\: ``email``: The email address of the user.
:currentPassword\: ``string``: The current password of the user to confirm the action.


------------------------------------------
Update the password of the authorized user
------------------------------------------

::

  POST /api/v1/me/password

Modify the password of the user that is currently authorized. The request can return one of the following status codes:

:200 OK: with the updated user object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:422 Unprocessable Entity: if the body parameters are invalid.

Body parameters
---------------

:password\: ``string``: The new password of the user.
:currentPassword\: ``string``: The current password of the user to confirm the action.


--------------------------
Delete the authorized user
--------------------------

::

  DELETE /api/v1/me

Delete the user that is currently authorized and all associated images permanently.

.. warning::

   When you delete a user, their associated images and other resources will be deleted permanently. This action is irreversible!

The request can return one of the following status codes:

:204 No Content: on success.
:401 Unauthorized: if the request doesn't contain authorization.

Body parameters
---------------

:currentPassword\: ``string``: The current password of the user to confirm the action.
