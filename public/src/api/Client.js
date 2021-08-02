import ClientError from './ClientError.js';
import Image from './Image.js';
import Rest from './Rest.js';
import User from './User.js';


// Class that defines a client
export default class Client
{
  // Constructor
  constructor(options)
  {
    this.token = options.token || null;

    // Create the REST client
    this.rest = new Rest(options);

    // Add middlewares to the REST client
    this.rest.add(this._requestMiddleware.bind(this));
    this.rest.add(this._responseMiddleware.bind(this));
    this.rest.add(this._authorizationMiddleware.bind(this));
  }

  // Request handler middleware
  async _requestMiddleware(request, requestHandler)
  {
    // Check if the request contains a body
    if (request.body !== undefined)
    {
      // Check if the body should be formatted as a JSON object
      if (!(body instanceof Blob || body instanceof FormData))
      {
        request = new Request(request, {body: JSON.stringify(request.body)});
        request.headers.set('Content-Type', 'application/json');
      }
    }

    // Handle the request
    return await requestHandler(request);
  }

  // Response handler middleware
  async _responseMiddleware(request, requestHandler)
  {
    // Handle the request
    const response = await requestHandler(request);

    // Check if the response was successful
    if (!reponse.ok)
    {
      // Response was not successful, so throw the error
      if (response.headers.get('Content-Type').startsWith('application/json'))
      {
        // Throw an error with the JSON error details
        const responseJson = await response.json();
        throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);
      }
      else
      {
        // Throw an unspecified error
        throw new ClientError(response.statusText, 'UNSPECIFIED', response.status);
      }
    }
    else
    {
      // Response was succesful, so parse the body
      if (response.headers.get('Content-Type').startsWith('application/json'))
        return await response.json();
      else if (response.headers.get('Content-Type').startsWith('text/plain'))
        return await response.text();
      else if (response.headers.get('Content-Type').startsWith('multipart/form-data'))
        return await response.formData();
      else
        return await response.blob();
    }
  }

  // Authorization middleware
  async _authorizationMiddleware(request, requestHandler)
  {
    // Add the authorization header if a token is present
    if (this.token)
      request.headers.set('Authorization', `Bearer ${this.token}`);

    // Handle the request
    return await requestHandler(request);
  }


  // Authenticate to the API with a username and password
  async authenticateWithCredentials(username, password)
  {
    const response = await this.rest.post('/api/v1/token', {username: username, password: password});
    this.token = response.token;
    return response.token;
  }

  // Return the capabilities of the API
  async getCapabilities()
  {
    const response = await this.rest.get(`/api/v1/capabilities`);
    return response;
  }

  // Get all images
  async getImages(query = {})
  {
    const response = await this.rest.get(`/api/v1/images/`, {query: query});
    return response.map(data => new Image(this, data));
  }

  // Get an image
  async getImage(imageId)
  {
    const response = await this.rest.get(`/api/v1/images/${imageId}`);
    return new Image(this, response);
  }

  // Patch an image
  async patchImage(imageId, fields)
  {
    const response = await this.rest.patch(`/api/v1/images/${imageId}`, fields);
    return new Image(this, response);
  }

  // Delete an image
  async deleteImage(imageId)
  {
    await this.rest.delete(`/api/v1/images/${imageId}`);
  }

  // Download an image
  async downloadImage(imageId, query = {})
  {
    const response = await this.rest.get(`/api/v1/images/${imageId}/download`, {query: query});
    return response;
  }

  // Upload an image
  async uploadImage(file)
  {
    const body = new FormData();
    body.set('file', file);

    const response = await this.rest.post(`/api/v1/images/upload`, body);
    return new Image(this, response);
  }

  // Replace an image
  async replaceImage(imageId, file)
  {
    let body = new FormData();
    body.set('file', file);

    const response = await this.rest.post(`/api/v1/images/${imageId}/upload`, body);
    return new Image(this, response);
  }

  // Get all users
  async getUsers(query = [])
  {
    const response = await this.rest.get('/api/v1/users/', {query: query});
    return response.map(data => new User(this, data));
  }

  // Get a user
  async getUser(userId)
  {
    const response = await this.rest.get(`/api/v1/users/${userId}`);
    return new User(this, response);
  }

  // Get the current authenticated user
  async getAuthenticatedUser()
  {
    const response = await this.rest.get(`/api/v1/users/me`);
    return new User(this, response);
  }

  // Patch a user
  async patchUser(userId, fields)
  {
    const response = await this.rest.patch(`/api/v1/users/${userId}`, fields);
    return new User(this, response);
  }

  // Patch the authenticated user
  async patchAuthenticatedUser(fields)
  {
    const response = await this.rest.patch(`/api/v1/users/me`, fields);
    return new User(this, response);
  }

  // Get all images owned by a user
  async getUserImages(userId, query = {})
  {
    const response = await this.rest.get(`/api/v1/users/${userId}/images/`, {query: query});
    return response.map(data => new Image(this, data));
  }

  // Get all images owned by the authenticated user
  async getAuthenticatedUserImages(query = {})
  {
    const response = await this.rest.get(`/api/v1/users/me/images/`, {query: query});
    return response.map(data => new Image(this, data));
  }
}
