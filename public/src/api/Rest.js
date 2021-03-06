// Class that defines a simple HTTP and REST API client built on the Fetch API
export default class Rest
{
  // Constructor
  constructor(options = {})
  {
    this.base = options.base || '';
    this.fetch = options.fetch || fetch;
  }

  // Add middleware to the middleware stack
  add(middleware)
  {
    const fetch = this.fetch;
    this.fetch = (url, init) => middleware(url, init, fetch);
  }

  // Send a request and return the response
  async request(method, url, options = {})
  {
    // Add the query to the url if specified
    if (options.query !== undefined && Object.keys(options.query).length > 0)
    {
      let query = new URLSearchParams();
      for (let [key, value] of Object.entries(options.query))
        query.set(key, String(value));
      url += '?' + query.toString();
    }

    // Create the request URL
    const requestUrl = this.base + url;

    // Create the request options
    const requestInit = {
      method: method,
      headers: new Headers(options.headers),
      body: options.body,
      mode: 'cors',
      credentials: 'omit'
    };

    // Send the request
    return await this.fetch(requestUrl, requestInit);
  }

  // Send a HEAD request
  async head(url, options = {})
  {
    return await this.request('HEAD', url, options);
  }

  // Send a GET request
  async get(url, options = {})
  {
    return await this.request('GET', url, options);
  }

  // Send a POST request
  async post(url, body, options = {})
  {
    Object.assign(options, {body});
    return await this.request('POST', url, options);
  }

  // Send a PUT request
  async put(url, body, options = {})
  {
    Object.assign(options, {body});
    return await this.request('PUT', url, options);
  }

  // Send a PATCH request
  async patch(url, body, options = {})
  {
    Object.assign(options, {body});
    return await this.request('PATCH', url, options);
  }

  // Send a DELETE request
  async delete(url, body, options = {})
  {
    Object.assign(options, {body});
    return await this.request('DELETE', url, options);
  }
}
