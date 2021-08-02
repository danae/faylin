import Client from './api/Client.js';
import ClientError from './api/ClientError.js';


// Create the client mixin
const client = {
  // The data for the mixin
  data: function() {
    return {
      // The client
      client: null,
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
      return this.clientAccessToken !== null;
    },
  },

  // Hook when the mixin is created
  created: function() {
    // Get the client options
    this.$options.clientBasePath ||= this.$router?.options.base.replace(/\/$/, '') || '';
    this.$options.clientAccessTokenKey ||= 'accessToken';

    // Get the client
    this.client = new Client({base: this.$options.clientBasePath});

    // Get the access token from the storage
    this.clientAccessToken = localStorage.getItem(this.$options.clientAccessTokenKey);
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

  // The methods for the mixin
  methods: {
    // Send a request and handle errors
    $request: async function(promise, options) {
      // Get the options
      const onSuccess = options.onSuccess || 'client-success';
      const onError = options.onError || 'client-error';
      const onUnauthorized = options.onUnauthorized || 'client-unauthorized';

      // Try to send the request
      try
      {
        // Send the request and get the response
        const response = await promise;

        // Emit the success event
        this.$emit(onSuccess, response);

        // Return the response
        return response;
      }
      catch (error)
      {
        // Check if the error is a client error
        if (error instanceof ClientError)
        {
          // Check if the error is an unauthorized error
          if (error instanceof ClientError && error.type === 'UNAUTHORIZED')
            this.$emit(onUnauthorized, error);

          // Otherwise emit the error event
          else
            this.$emit(onError, error);
        }
        else
        {
          // Rethrow errors that are no client errors
          throw error;
        }
      }
    },

    // Functions for logging in and out
    $login: async function(username, password) {
      // Set the token
      this.clientAccessToken = await this.$request(this.client.authorizeWithUserCredentials(username, password), {onSuccess: 'login-success'}) || null;
    },
    $logout: async function() {
      // Reset the token
      this.clientAccessToken = null;
    },

    // Functions for making requests to the client
    $getImages: async function(query = {}) {
      return await this.$request(this.client.getImages(query), {onSuccess: 'get-images-success'});
    },
    $getImage: async function(imageId) {
      return await this.$request(this.client.getImage(imageId), {onSuccess: 'get-image-success'});
    },
    $patchImage: async function(imageId, fields) {
      return await this.$request(this.client.patchImage(imageId, fields), {onSuccess: 'patch-image-success'});
    },
    $deleteImage: async function(imageId) {
      return await this.$request(this.client.deleteImage(imageId), {onSuccess: 'delete-image-success'});
    },
    $downloadImage: async function(imageId, query = {}) {
      return await this.$request(this.client.downloadImage(imageId, query), {onSuccess: 'download-image-success'});
    },
    $uploadImage: async function(file) {
      return await this.$request(this.client.uploadImage(file), {onSuccess: 'upload-image-success'});
    },
    $replaceImage: async function(imageId, file) {
      return await this.$request(this.client.replaceImage(imageId, file), {onSuccess: 'replace-image-success'});
    },
    $getUsers: async function(query = {}) {
      return await this.$request(this.client.getUsers(query), {onSuccess: 'get-users-success'});
    },
    $getUser: async function(userId) {
      return await this.$request(this.client.getUser(userId), {onSuccess: 'get-user-success'});
    },
    $getAuthenticatedUser: async function() {
      return await this.$request(this.client.$getAuthenticatedUser(), {onSuccess: 'get-authenticated-user-success'});
    },
    $patchUser: async function(userId, fields) {
      return await this.$request(this.client.patchUser(userId, fields), {onSuccess: 'patch-user-success'});
    },
    $patchAuthenticatedUser: async function(userId, fields) {
      return await this.$request(this.client.patchUser(userId, fields), {onSuccess: 'patch-authenticated-user-success'});
    },
    $getUserImages: async function(userId, query = {}) {
      return await this.$request(this.client.getUserImages(userId, query), {onSuccess: 'get-user-images-success'});
    },
    $getAuthenticatedUserImages: async function(query = {}) {
      return await this.$request(this.client.getAuthenticatedUserImages(query), {onSuccess: 'get-authenticated-user-images-success'});
    },
  },
};

// Export the client mixin
export default client;
