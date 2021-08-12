import Image from './Image.js';


// Class that defines a user
export default class User
{
  // Constructor
  constructor(data)
  {
    this.update(data);
  }

  // Update the user from a data array
  update(data)
  {
    this.id = data.id;
    this.email = data.email;
    this.name = data.name;
    this.description = data.description;
    this.public = data.public;
    this.avatar = data.avatar;
    this.createdAt = data.createdAt instanceof Date ? data.createdAt : new Date(data.createdAt);
    this.updatedAt = data.updatedAt instanceof Date ? data.updatedAt : new Date(data.updatedAt);
  }

  // Return the string representation of the image
  toString()
  {
    return this.name;
  }
}
