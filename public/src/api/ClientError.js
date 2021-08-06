// Class that defines a client error
export default class ClientError extends Error
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
