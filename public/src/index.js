import client from './client.js';
import components from './components.js';
import router from './router.js';


// Create the Vue app
const app = new Vue({
  el: '#app',
  router: router,

  // Mixins for the app
  mixins: [client],

  // The methods for the app
  methods: {
    // Event handler when a client request returns an error
    onClientError: function(error)
    {
      // Display the error message
      this.$displayErrorMessage(error.message);
    },

    // Event handler when a client request is unauthorized
    onClientUnauthorized: function(error)
    {
      // Display the error message
      this.$displayErrorMessage('Unauthorized: ' + error.message);
    },
  },

  // Hook for when the app is created
  created: function()
  {
    // Add an event listener for a client request that returns an error or is unauthorized
    this.$on('client-error', this.onClientError);
    this.$on('client-unauthorized', this.onClientUnauthorized);
  },

  // Hook for when the app is mounted
  mounted: function()
  {
    // Parse the body through Twemoji
    this.$nextTick(function() {
      twemoji.parse(document.body);
    });
  },

  // Hook for when the app is updated
  updated: function()
  {
    // Parse the body through Twemoji
    this.$nextTick(function() {
      twemoji.parse(document.body);
    });
  }
});


// Function to return an icon text span
Vue.prototype.$iconText = function(icon, message)
{
  return `<span class="icon-text"><span class="icon"><i class="fas fa-${icon}"></i></span><span>${message}</span></span>`;
}

// Function to display a message
Vue.prototype.$displayMessage = function(message)
{
  console.log('%c info %c ' + message, 'color: white; background: black; padding: 1px; border-radius: 3px', 'background: transparent');
  this.$buefy.toast.open({message: message, type: 'is-dark', position: 'is-top', duration: 2000});
}

// Function to display a warning message
Vue.prototype.$displayWarningMessage = function(message)
{
  console.log('%c warning %c %c' + message, 'color: white; background: #f4bd00; padding: 1px; border-radius: 3px', 'background: transparent', 'color: #5c3c00');
  this.$buefy.toast.open({message: message, type: 'is-warning', position: 'is-top', duration: 5000});
}

// Function to display an error message
Vue.prototype.$displayErrorMessage = function(message)
{
  console.log('%c error %c %c' + message, 'color:white; background:#ff0000; padding: 1px; border-radius: 3px', 'background: transparent', 'color: #ff0000');
  this.$buefy.toast.open({message: message, type: 'is-danger', position: 'is-top', duration: 5000});
}
