import User from './User.js';


// Class that defines a session
export default class Session
{
  // Constructor
  constructor(data)
  {
    this.update(data);
  }

  // Update the session from a data array
  update(data)
  {
    this.id = data.id;
    this.createdAt = data.createdAt instanceof Date ? data.createdAt : new Date(data.createdAt);
    this.updatedAt = data.updatedAt instanceof Date ? data.updatedAt : new Date(data.updatedAt);
    this.user = data.user instanceof User ? data.user : new User(data.user);
    this.userAgent = data.userAgent;
    this.userAddress = data.userAddress;
    this.current = data.current;
    this.info = data.info;
  }
}
