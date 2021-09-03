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
    this.name = data.name;
    this.createdAt = data.createdAt instanceof Date ? data.createdAt : new Date(data.createdAt);
    this.updatedAt = data.updatedAt instanceof Date ? data.updatedAt : new Date(data.updatedAt);
    this.email = data.email;
    this.title = data.title;
    this.description = data.description;
    this.public = data.public;
    this.avatar = data.avatar;
  }

  // Return the string representation of the user
  toString()
  {
    return this.title;
  }
}
