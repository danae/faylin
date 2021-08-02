// Class that defines a user
export default class User
{
  // Constructor
  constructor(client, data)
  {
    this.client = client;

    this.id = data.id;
    this.name = data.name;
    this.email = data.email;
    this.createdAt = new Date(data.createdAt);
    this.updatedAt = new Date(data.updatedAt);
  }

  // Return the string representation of the image
  toString()
  {
    return this.name;
  }
}
