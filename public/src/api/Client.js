import ClientError from './ClientError.js';
import Image from './Image.js';
import Rest from './Rest.js';
import User from './User.js';


// Class that defines a client
export default class Client
{
  // Constructor
  constructor(options = {})
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
  async _requestMiddleware(url, init, next)
  {
    // Check if the request contains a body
    if (init.body !== undefined)
    {
      // Check if the body should be formatted as a JSON object
      if (!(init.body instanceof Blob || init.body instanceof FormData))
      {
        init.body = JSON.stringify(init.body);
        init.headers.set('Content-Type', 'application/json');
      }
    }

    // Handle the request
    return await next(url, init);
  }

  // Response handler middleware
  async _responseMiddleware(url, init, next)
  {
    // Handle the request
    const response = await next(url, init);

    // Check if the response was successful
    if (!response.ok)
    {
      // Response was not successful, so throw the error
      if (response.headers.get('Content-Type')?.startsWith('application/json'))
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
      if (response.status === 204)
        return null;
      if (response.headers.get('Content-Type')?.startsWith('application/json'))
        return await response.json();
      else if (response.headers.get('Content-Type')?.startsWith('text/plain'))
        return await response.text();
      else if (response.headers.get('Content-Type')?.startsWith('multipart/form-data'))
        return await response.formData();
      else
        return await response.blob();
    }
  }

  // Authorization middleware
  async _authorizationMiddleware(url, init, next)
  {
    // Add the authorization header if a token is present
    if (this.token)
      init.headers.set('Authorization', `Bearer ${this.token}`);

    // Handle the request
    return await next(url, init);
  }


  // Authenticate to the API with a username and password
  async authorizeWithUserCredentials(username, password)
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
    return response.map(data => new Image(data));
  }

  // Get an image
  async getImage(imageId)
  {
    const response = await this.rest.get(`/api/v1/images/${imageId}`);
    return new Image(response);
  }

  // Patch an image
  async patchImage(imageId, fields)
  {
    const response = await this.rest.patch(`/api/v1/images/${imageId}`, fields);
    return new Image(response);
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
    return new Image(response);
  }

  // Replace an image
  async replaceImage(imageId, file)
  {
    let body = new FormData();
    body.set('file', file);

    const response = await this.rest.post(`/api/v1/images/${imageId}/upload`, body);
    return new Image(response);
  }

  // Get all users
  async getUsers(query = [])
  {
    const response = await this.rest.get('/api/v1/users/', {query: query});
    return response.map(data => new User(data));
  }

  // Get a user
  async getUser(userId)
  {
    const response = await this.rest.get(`/api/v1/users/${userId}`);
    return new User(response);
  }

  // Patch a user
  async patchUser(userId, fields)
  {
    const response = await this.rest.patch(`/api/v1/users/${userId}`, fields);
    return new User(response);
  }

  // Get all images owned by a user
  async getUserImages(userId, query = {})
  {
    const response = await this.rest.get(`/api/v1/users/${userId}/images/`, {query: query});
    return response.map(data => new Image(data));
  }


  // Get the current authenticated user
  async getMe()
  {
    const response = await this.rest.get(`/api/v1/me`);
    return new User(response);
  }

  // Patch the authenticated user
  async patchMe(fields)
  {
    const response = await this.rest.patch(`/api/v1/me`, fields);
    return new User(response);
  }
  // Get all images owned by the authenticated user
  async getMeImages(query = {})
  {
    const response = await this.rest.get(`/api/v1/me/images/`, {query: query});
    return response.map(data => new Image(data));
  }
}
