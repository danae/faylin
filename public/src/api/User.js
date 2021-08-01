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

  // Patch the user
  async patch()
  {
    await this.client.patchUser(this.id, {name: this.name, email: this.email});
  }

  // Get all images owned by the user
  async getImages(query = {})
  {
    return await this.client.getUserImages(this.id, query);
  }

  // Return the string representation of the image
  toString()
  {
    return `User ${this.id} with name "${this.name}"`;
  }
}
