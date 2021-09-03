import User from './User.js';


// Class that defines an image
export default class Image
{
  // Constructor
  constructor(data)
  {
    this.update(data);
  }

  // Update the image from a data array
  update(data)
  {
    this.id = data.id;
    this.name = data.name;
    this.createdAt = data.createdAt instanceof Date ? data.createdAt : new Date(data.createdAt);
    this.updatedAt = data.updatedAt instanceof Date ? data.updatedAt : new Date(data.updatedAt);
    this.user = data.user instanceof User ? data.user : new User(data.user);
    this.title = data.title;
    this.description = data.description;
    this.public = data.public;
    this.nsfw = data.nsfw;
    this.contentType = data.contentType;
    this.contentLength = data.contentLength;
    this.downloadUrl = data.downloadUrl;
    this.thumbnailUrl = data.thumbnailUrl;
  }

  // Return the string representation of the image
  toString()
  {
    return this.title;
  }
}
