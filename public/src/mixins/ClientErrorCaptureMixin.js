import ClientError from '../api/ClientError.js';


// Client error capture mixin component
export default {
  // Hook when an error is captured
  errorCaptured: function(error, vm, info) {
    // Check if the error is a client error
    if (error instanceof ClientError)
    {
      // Check if the error is an unautorized client error
      if (error.type === 'UNAUTHORIZED')
        this.$root.$emit('client-unauthorized', error);
      else
        this.$root.$emit('client-error', error);

      // Stop propagating the error
      return false;
    }

    // Otherwise bubble the error
    return true;
  },
};
