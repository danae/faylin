============
Capabilities
============

The capabilities object contains useful information for clients regarding the configuration of the Faylin instance.

-----------------------
The capabilities object
-----------------------

The capabilities object has the following fields:

:supportedContentTypes\: ``array<string>``: An array of supported content types for an upload as MIME types.
:supportedSize\: ``integer``: The maximal supported size for an uploaded image in bytes.


--------------------
Get the capabilities
--------------------

::

  GET /api/v1/capabilitites

Fetches the capabilitites. The request can return one of the following status codes:

:200 OK: with the fetched capabilities object as body on success.
:401 Unauthorized: if the request doesn't contain authorization.
