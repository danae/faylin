import User from './User.js';


// Class that defines an image
export default class Image
{
  // Constructor
  constructor(client, data)
  {
    this.client = client;

    this.id = data.id;
    this.name = data.name;
    this.contentType = data.contentType;
    this.contentLength = data.contentLength;
    this.user = new User(this.client, data.user);
    this.createdAt = new Date(data.createdAt);
    this.updatedAt = new Date(data.updatedAt);
    this.downloadUrl = data.downloadUrl;
  }

  // Return the string representation of the image
  toString()
  {
    return this.name;
  }
}
