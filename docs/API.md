# API documentation

This document lists the different object resources and endpoints in the Faylin API.

## Authorization

All endpoints in the API require authorization, which must be provided with a basic authorization header containing the name and password of the user who makes the request.

## Capabilities

The API has restrictions on the supported content types and size of uploaded files, which can be fetched through the capabilities endpoint.

### Capabilities structure

Field | Type | Description
--- | --- | ---
supportedContentTypes | array of strings | An array of supported content types for an upload as MIME types.
supportedSize | integer | The maximal supported file size for an upload in bytes.


### Get Capabilities

`GET /api/v1/capabilities`

Fetches the capabilities of the API. Returns:
- `200 OK` with the capabilities as body on success;
- `401 Unauthorized` if the request doesn't contain authorization.


## Users

A user represents a user account along with its associated metadata. Users can own images and other resources.

### User structure

Field | Type | Description
--- | --- | ---
id | string | The id of the user.
name | string | The name of the user.
email | string | The email address of the user.
createdAt | datetime | The date the user was created.
updatedAt | datetime | The date the user was last updated.


### List users

`GET /api/v1/users/`

Fetches all users. Returns:
- `200 OK` with an array of the fetched users objects on success;
- `400 Bad Request` if the query parameters are invalid;
- `401 Unauthorized` if the request doesn't contain authorization.

#### Query parameters

Field | Type | Description
--- | --- | ---
sort? | string | A comma-separated list of fields to sort the fetched users by. optionally precided by a minus sign to indicate descending order. Defaults to `"-createdAt"`.
page? | integer | The page of the fetched users to return, to use with pagination. This field must be used in conjunction with the perPage field. Defaults to `0`.
perPage? | integer | The number of fetched users to return, to use with pagination. This field must be used in conjunction with the page field. Defaults to `20`.


### Get a user

`GET /api/v1/users/{user.id}`

Fetches the user for the given id. Returns:
- `200 OK` with the fetched user object as body on success;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to fetch the user;
- `404 Not Found` if no user with the given id exists.


### Get the authorized user

`GET /api/v1/users/me`

Fetches the user that is currently authorized. Returns:
- `200 OK` with the fetched user object as body on success;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to fetch the user.


### Modify a user

`PATCH /api/v1/users/{user.id}`

Modify the metadata of a user. Returns:
- `200 OK` with the updated image object as body on success;
- `400 Bad Request` if the body parameters are invalid;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to modify the user;
- `404 Not Found` if no user with the given id exists.


### Modify the authorized user

`PATCH /api/v1/users/me`

Modify the metadata of the user that is currently authorized. Returns:
- `200 OK` with the updated image object as body on success;
- `400 Bad Request` if the body parameters are invalid;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to modify the user.

#### Body parameters

Field | Type | Description
--- | --- | ---
name | string | The name of the user.
email | string | The email address of the user.


## Images

An image represents an uploaded image file along with its associated metadata.

### Image structure

Field | Type | Description
--- | --- | ---
id | string | The id of the image.
name | string | The name of the image.
contentType | string | The content type of the image as a MIME type.
contentLength | integer | The content size of the image in bytes.
user | user | Representation of the user that owns the image.
createdAt | datetime | The date the image was created.
updatedAt | datetime | The date the image was last updated.
redirectUrl | url | URL specifying where to get the contents of the image.


### List images

`GET /api/v1/images/`

Fetches all images. Returns:
- `200 OK` with an array of the fetched images on success;
- `400 Bad Request` if the query parameters are invalid;
- `401 Unauthorized` if the request doesn't contain authorization.

#### Query parameters

Field | Type | Description
--- | --- | ---
sort? | string | A comma-separated list of fields to sort the fetched images by. optionally precided by a minus sign to indicate descending order. Defaults to `"-createdAt"`.
page? | integer | The page of the fetched images to return, to use with pagination. This field must be used in conjunction with the perPage field. Defaults to `0`.
perPage? | integer | The number of fetched images to return, to use with pagination. This field must be used in conjunction with the page field. Defaults to `20`.


### List images that are owned by a user

`GET /api/v1/users/{user.id}/images/`

Fetches all images that are owned by the user for the given id. Returns:
- `200 OK` with an array of the fetched images on success;
- `400 Bad Request` if the query parameters are invalid;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to fetch the user;
- `404 Not Found` if no user with the given id exists.

#### Query parameters

The query parameters for this request are the same as the `GET /api/v1/images/` endpoint.


### List images that are owned by the authorized user

`GET /api/v1/users/me/images/`

Fetches all images that are owned by the user that is currently authorized. Returns:
- `200 OK` with an array of the fetched images on success;
- `400 Bad Request` if the query parameters are invalid;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to fetch the user.

#### Query parameters

The query parameters for this request are the same as the `GET /api/v1/images/` endpoint.


### Get an image

`GET /api/v1/images/{image.id}`

Fetches the image for the given id. Returns:
- `200 OK` with the fetched image object as body on success;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to fetch the image;
- `404 Not Found` if no image with the given id exists.


### Modify an image

`PATCH /api/v1/images/{image.id}`

Modify the metadata of an image. Returns:
- `200 OK` with the updated image object as body on success;
- `400 Bad Request` if the body parameters are invalid;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to modify the image;
- `404 Not Found` if no image with the given id exists.

#### Body parameters

Field | Type | Description
--- | --- | ---
name | string | The name of the image.


### Delete an image

`DELETE /api/v1/images/{image.id}`

Delete an image permanently. Returns:
Modify the metadata of an image. Returns:
- `204 No Content` on success;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to delete the image;
- `404 Not Found` if no image with the given id exists.

### Upload a new image

`POST /api/v1/images/upload`

Uploads a new image. Returns:
- `201 Created` with the newly created image object as body on success;
- `400 Bad Request` if the body parameters are invalid;
- `401 Unauthorized` if the request doesn't contain authorization;
- `413 Payload Too Large` if the content size of the uploaded file is too large, see the capabilities endpoint to fetch the supported size;
- `415 Unsupported Media Type` if the content type of the uploaded file is unsupported, see the capabilities endpoint to fetch the supported content types.

#### Body parameters

The request must contain a body of type `multipart/form-data` containing an uploaded file with name `file`.


### Upload a replacement for an existing image

`POST /api/v1/images/{image.id}/upload`

Replaces an existing image. Returns:
- `201 Created` with the updated image object as body on success;
- `400 Bad Request` if the body parameters are invalid;
- `401 Unauthorized` if the request doesn't contain authorization;
- `403 Forbidden` if the authorized user is not allowed to replace the image;
- `404 Not Found` if no image with the given id exists;
- `413 Payload Too Large` if the content size of the uploaded file is too large, see the capabilities endpoint to fetch the supported size;
- `415 Unsupported Media Type` if the content type of the uploaded file is unsupported, see the capabilities endpoint to fetch the supported content types.

#### Body parameters

The request must contain a body of type `multipart/form-data` containing an uploaded file with name `file`.
