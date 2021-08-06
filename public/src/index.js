import components from './components.js';
import router from './router.js';

import ClientMixin from './mixins/ClientMixin.js';


// Create the Vue app
const app = new Vue({
  el: '#app',
  router: router,

  // Mixins for the app
  mixins: [ClientMixin],

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
    // Register event handlers for a client request that returns an error or is unauthorized
    this.$on('error', this.onClientError.bind(this));
    this.$on('unauthorized', this.onClientUnauthorized.bind(this));
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


// Register a global method to return an icon text span
Vue.prototype.$iconText = function(icon, message) {
  return `<span class="icon-text"><span class="icon"><i class="fas fa-${icon}"></i></span><span>${message}</span></span>`;
}

// Register a global method to display a message
Vue.prototype.$displayMessage = function(message, type = 'is-dark', duration = 2000) {
  console.log('%c info %c ' + message, 'color: white; background: black; padding: 1px; border-radius: 3px', 'background: transparent');

  this.$buefy.toast.open({message: message, type, duration});
}

// Register a global method to display a warning
Vue.prototype.$displayWarning = function(error) {
  console.groupCollapsed('%c warning %c %c' + error.message, 'color: white; background: #f4bd00; padding: 1px; border-radius: 3px', 'background: transparent', 'color: #5c3c00');
  console.warn(error);
  console.groupEnd();

  this.$buefy.toast.open({message: error.message, type: 'is-warning', position: 'is-top', duration: 5000});
}

// Register a global method to display an error
Vue.prototype.$displayError = function(error) {
  console.groupCollapsed('%c error %c %c' + error.message, 'color:white; background:#ff0000; padding: 1px; border-radius: 3px', 'background: transparent', 'color: #ff0000');
  console.error(error);
  console.groupEnd();

  this.$buefy.toast.open({message: error.message, type: 'is-danger', position: 'is-top', duration: 5000});
}
