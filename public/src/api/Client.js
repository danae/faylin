import Image from './Image.js';
import User from './User.js';


// Class that defines a client
export default class Client
{
  // Constructor
  constructor(options)
  {
    this.baseUrl = options.baseUrl || "";
    this.token = options.token || null;
  }


  // Return an authorization header with the token
  get headers()
  {
    let headers = new Headers();
    if (this.token !== null)
      headers.set('Authorization', `Bearer ${this.token}`);
    return headers;
  }


  // Make a request and return the response
  async request(method, url, options = {})
  {
    // Initialize the request
    let init = {method: method};

    // Create the headers for the request
    if (options.headers !== undefined)
      init.headers = new Headers(options.headers);
    else
      init.headers = new Headers();

    // Create the body for the request
    if (options.body !== undefined)
    {
      // Check if the body is an instance of FormData
      if (options.body instanceof FormData)
      {
        init.body = options.body;
      }

      // Check if the body is JSON
      else if (typeof options.body === 'object')
      {
        init.body = JSON.stringify(options.body);
        init.headers.set('Content-Type', 'application/json');
      }

      // Otherwise the body is invalid
      else
        throw new TypeError("Body must be either an instance of FormData or an object or array");
    }

    // Create the url for the request
    url = this.baseUrl + url;
    if (options.query !== undefined)
    {
      let query = new URLSearchParams();
      for (let [key, value] of Object.entries(options.query))
        query.set(key, String(value));

      url += '?' + query.toString();
    }

    // Execute and return the request
    return await fetch(url, init);
  }

  // Make a GET request
  async get(url, options = {})
  {
    return await this.request('GET', url, options);
  }

  // Make a POST request
  async post(url, body, options = {})
  {
    Object.assign(options, {body: body});
    return await this.request('POST', url, options);
  }

  // Make a PATCH request
  async patch(url, body, options = {})
  {
    Object.assign(options, {body: body});
    return await this.request('PATCH', url, options);
  }

  // Make a DELETE request
  async delete(url, body, options = {})
  {
    Object.assign(options, {body: body});
    return await this.request('DELETE', url, options);
  }


  // Authenticate to the API with a username and password
  async authenticateWithCredentials(username, password)
  {
    let response = await this.post('/token', {username: username, password: password});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return responseJson.token;
  }


  // Return the capabilities of the API
  async getCapabilities()
  {
    let response = await this.get(`/capabilities`, {headers: this.headers});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return responseJson;
  }

  // Get all images
  async getImages(query = {})
  {
    let response = await this.get(`/images/`, {headers: this.headers, query: query});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return responseJson.map(data => new Image(this, data));
  }

  // Get an image
  async getImage(imageId)
  {
    let response = await this.get(`/images/${imageId}`, {headers: this.headers});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return new Image(this, responseJson);
  }

  // Patch an image
  async patchImage(imageId, fields)
  {
    let response = await this.patch(`/images/${imageId}`, fields, {headers: this.headers});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return new Image(this, responseJson);
  }

  // Delete an image
  async deleteImage(imageId)
  {
    let response = await this.delete(`/images/${imageId}`, {headers: this.headers});

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);
  }

  // Download an image
  async downloadImage(imageId, query = {})
  {
    let response = await this.get(`/images/${imageId}/download`, {headers: this.headers, query: query});
    let responseBlob = await response.blob();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return responseBlob;
  }

  // Upload an image
  async uploadImage(file)
  {
    let body = new FormData();
    body.set('file', file);

    let response = await this.post(`/images/upload`, body, {headers: this.headers});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return new Image(this, responseJson);
  }

  // Replace an image
  async replaceImage(imageId, file)
  {
    let body = new FormData();
    body.set('file', file);

    let response = await this.post(`/images/${imageId}/upload`, body, {headers: this.headers});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return new Image(responseJson);
  }

  // Get all users
  async getUsers(query = [])
  {
    let response = await this.get('/users/', {headers: this.headers, query: query});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return responseJson.map(data => new User(this, data));
  }

  // Get a user
  async getUser(userId)
  {
    let response = await this.get(`/users/${userId}`, {headers: this.headers});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return new User(this, responseJson);
  }

  // Get the current authenticated user
  async getAuthenticatedUser()
  {
    let response = await this.get(`/users/me`, {headers: this.headers});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return new User(this, responseJson);
  }

  // Patch a user
  async patchUser(userId, fields)
  {
    let response = await this.patch(`/user/${userId}`, fields, {headers: this.headers});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return new User(this, responseJson);
  }

  // Patch the authenticated user
  async patchAuthenticatedUser(fields)
  {
    let response = await this.patch(`/user/me`, fields, {headers: this.headers});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return new User(this, responseJson);
  }

  // Get all images owned by a user
  async getUserImages(userId, query = {})
  {
    let response = await this.get(`/users/${userId}/images/`, {headers: this.headers, query: query});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return responseJson.map(data => new Image(this, data));
  }

  // Get all images owned by the authenticated user
  async getAuthenticatedUserImages(query = {})
  {
    let response = await this.get(`/users/me/images/`, {headers: this.headers, query: query});
    let responseJson = await response.json();

    if (!response.ok)
      throw new ClientError(responseJson.error.description, responseJson.error.type, response.status);

    return responseJson.map(data => new Image(this, data));
  }
}


// Class that defines a client error
export class ClientError extends Error
{
  // Constructor
  constructor(message, type, status)
  {
    super(message);
    this.type = type;
    this.status = status;
  }

  // Return the string representation of the error
  toString()
  {
    return this.message;
  }
}
