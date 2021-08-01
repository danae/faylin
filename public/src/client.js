import Client from './api/Client.js';


// Create the client
const client = new Client({
  baseUrl: '/php/faylin-slim/api/v1',
  token: localStorage.getItem('token'),
});

// Register the client
Object.defineProperty(Vue.prototype, '$client', {
  get: function() { return client }
});

// Export the client
export default client;
