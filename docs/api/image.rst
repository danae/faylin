==============
Image resource
==============

An image represents an uploaded image file along with its associated metadata.


---------------------------
Restrictions on image names
---------------------------

The following restrictions are enforced on image namesand titles:

- Image names must be between 3 and 32 characters long and can only contain A-Z, a-z, 0-9, the hyphen and underscore.
- Image titles (display names) must be between 1 and 64 characters long. They can contain most valid Unicode characters.


----------------
The image object
----------------

An image object has the following fields:

:id\: ``string``: The identifier of the image.
:name\: ``string``: The name of the image, which is used in the download URL. Minimal 3 and maximal 32 characters long and can only contain ``[A-Za-z0-9-_]``.
:createdAt\: ``datetime``: The date the image was created.
:updatedAt\: ``datetime``: The date the image was last updated.
:user\: ``user``: The user that owns the image.
:title\: ``string``: The title of the image, which is used as the display name. Minimal 1 and maximal 64 characters long.
:description\: ``string``: The description of the image. maximal 256 characters long.
:public\: ``boolean```: Indicates if the image is listed publicly.
:nsfw\: ``boolean``: Indicates if the image should be hidden for users with the mature content filter turned on.
:contentType\: ``string``: The content type of the image as a MIME type.
:contentLength\: ``integer``: The content size of the image in bytes.
:checksum\: ``string``: The SHA-256 checksum of the contents of the image.
:downloadUrl\: ``url``: The location that contains the contents of the image.


-----------
List images
-----------

::

  GET /api/v1/images/

Fetches all images. The request can return one of the following status codes:

:200 OK: with an array of the fetched images on success.
:401 Unauthorized: if the request doesn't contain authorization.
:422 Unprocessable Entity: if the query parameters are invalid.

Query parameters
----------------

:sort\: ``string?``: A comma-separated list of fields to sort the fetched images by. optionally precided by a minus sign to indicate descending order. Defaults to ``"-createdAt"``.
:page\: ``integer?``: The page of the fetched images to return, to use with pagination. This field must be used in conjunction with the perPage field. Defaults to ``0``.
:perPage\: ``integer?``: The number of fetched images to return, to use with pagination. This field must be used in conjunction with the page field. Defaults to ``20``.


------------------------------------
List images that are owned by a user
------------------------------------

::

  GET /api/v1/users/{user.id}/images/

Fetches all images that are owned by the user for the given id. The request can return one of the following status codes:

:200 OK: with an array of the fetched images on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to fetch the images.
:404 Not Found: if no user with the given identifier exists.
:422 Unprocessable Entity: if the query parameters are invalid.

Query parameters
----------------

The query parameters for this request are the same as the `GET /api/v1/images/` endpoint.


-------------------------------------------------
List images that are owned by the authorized user
-------------------------------------------------

::

  GET /api/v1/me/images/

Fetches all images that are owned by the user that is currently authorized. The request can return one of the following status codes:

:200 OK: with an array of the fetched images on success.
:401 Unauthorized: if the request doesn't contain authorization.
:422 Unprocessable Entity: if the query parameters are invalid.

Query parameters
----------------

The query parameters for this request are the same as the `GET /api/v1/images/` endpoint.


------------
Get an image
------------

::

  GET /api/v1/images/{image.id}

Fetches the image for the given identifier. The request can return one of the following status codes:

:200 OK: with the fetched image object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to fetch the image.
:404 Not Found: if no image with the given identifier exists.


---------------
Modify an image
---------------

::

  PATCH /api/v1/images/{image.id}

Modify the structure of an image. The request can return one of the following status codes:

:200 OK: with the updated image object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to modify the image.
:404 Not Found: if no image with the given identifier exists.
:422 Unprocessable Entity: if the body parameters are invalid.

Body parameters
---------------

:title\: ``string``: The title of the image, which is used as the display name. Minimal 1 and maximal 64 characters long.
:description\: ``string``: The description of the image. Maximal 256 characters long.
:public\: ``boolean``: Indicates if the image is listed publicly.
:nsfw\: ``boolean``: Indicates if the image should be hidden for users with the mature content filter turned on.

If any of the body parameters is absent, then that field will not be updated in the image object.


---------------
Delete an image
---------------

::

  DELETE /api/v1/images/{image.id}

Delete an image permanently.

.. warning::

   This action is irreversible!

The request can return one of the following status codes:

:204 No Content: on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to delete the image.
:404 Not Found: if no image with the given identifier exists.


------------------
Upload a new image
------------------

::

  POST /api/v1/images/upload

Uploads a new image containing the uploaded file provided in the body. The request can return one of the following status codes:

:201 Created: with the newly created image object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:413 Payload Too Large: if the content size of the uploaded file is too large, see the capabilities endpoint to fetch the supported size.
:415 Unsupported Media Type: if the content type of the uploaded file is unsupported, see the capabilities endpoint to fetch the supported content types.
:422 Unprocessable Entity: if the body parameters are invalid.

Body parameters
---------------

The request must contain a body of type `multipart/form-data` containing an uploaded file with name `file`.


------------------------------------------
Upload a replacement for an existing image
------------------------------------------

::

  POST /api/v1/images/{image.id}/upload

Replaces the contents of an existing image with the uploaded file provided in the body. The request can return one of the following status codes:

:201 Created: with the updated image object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to modify the image.
:404 Not Found: if no image with the given identifier exists.
:413 Payload Too Large: if the content size of the uploaded file is too large, see the capabilities endpoint to fetch the supported size.
:415 Unsupported Media Type: if the content type of the uploaded file is unsupported, see the capabilities endpoint to fetch the supported content types.
:422 Unprocessable Entity: if the body parameters are invalid.

Body parameters
---------------

The request must contain a body of type `multipart/form-data` containing an uploaded file with name `file`.


----------------------------
Get the contents of an image
----------------------------

::

  GET /{image.id}[.{format}]

Fetches the contents of an image. The request can return one of the following status codes:

:200 OK: with the fetched image contents as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
:403 Forbidden: if the authorized user is not allowed to fetch the image.
:404 Not Found: if no image with the given identifier exists.
:422 Unprocessable Entity: if the route or query parameters are invalid.


Route parameters
----------------

:format\: ``string?``: The format corresponding to a content type to which the image contents will be converted, e.g. ``png`` or ``jpg``. Defaults to the format of the image itself.


Query parameters
----------------

:dl\: ``boolean?``: Indicates if the response should contain an ``Content-Disposition: inline`` header, so the contents will be downloaded isntead of shown. Defaults to ``false``.
:transform\: ``string?``: Transformations to ally to the image contents before returning it. Defaults to an empty string, which means that no transformations will be applied.

Image transformations
---------------------

*To be expanded*
