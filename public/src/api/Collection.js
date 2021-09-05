import Image from './Image.js';
import User from './User.js';


// Class that defines a collection
export default class Collection
{
  // Constructor
  constructor(data)
  {
    this.update(data);
  }

  // Update the collection from a data array
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
    this.images = data.images.map(image => image instanceof Image ? image : new Image(image));
  }

  // Return the string representation of the collection
  toString()
  {
    return this.title;
  }
}
