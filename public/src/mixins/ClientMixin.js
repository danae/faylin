import Client from '../api/Client.js';


// Client mixin component
export default {
  // The data for the mixin
  data: function() {
    return {
      // The client
      client: null,

      // The client user
      clientUser: null,
    }
  },

  // The computed data for the mixin
  computed: {
    // The access token of the client
    clientAccessToken: {
      get: function() {
        if (this.client)
          return this.client.token;
        else
          return null;
      },
      set: function(value)
      {
        if (this.client)
          this.client.token = value;
      },
    },

    // The logged in state of the client
    clientLoggedIn: function() {
      return this.clientUser !== null;
    },
  },

  // Hook when the mixin is created
  created: async function() {
    // Get the client options
    this.$options.clientBasePath ||= this.$router?.options.base.replace(/\/$/, '') || '';
    this.$options.clientAccessTokenKey ||= 'accessToken';

    // Get the client
    this.client = new Client({base: this.$options.clientBasePath});

    // Get the access token from the storage
    this.clientAccessToken = localStorage.getItem(this.$options.clientAccessTokenKey);

    // Get the client user
    if (this.clientAccessToken)
      this.clientUser = await this.client.getAuthorizedUser();
  },

  // The watchers for the mixin
  watch: {
    // Watch the token
    clientAccessToken: function(newClientAccessToken) {
      // Set or remove the access token in the storage
      if (newClientAccessToken !== null)
        localStorage.setItem(this.$options.clientAccessTokenKey, newClientAccessToken);
      else
        localStorage.removeItem(this.$options.clientAccessTokenKey);
    }
  },
};

// Register a global method to log in and set the access token
Vue.prototype.$login = async function(username, password) {
  this.$root.clientAccessToken = await this.$root.client.authorizeWithUserCredentials(username, password);
  this.$root.clientUser = await this.$root.client.getAuthorizedUser();
}

// Register a global method to log out and reset the access token
Vue.prototype.$logout = function() {
  this.$root.clientAccessToken = null;
  this.$root.clientUser = null;
}
