import client from './client.js';
import components from './components.js';
import router from './router.js';


// Create the Vue app
const app = new Vue({
  el: '#app',
  router: router,

  // Data for the app
  data: {
    // The token used for authorization
    token: null,
  },

  // Computed data for the app
  computed: {
    currentToken : function() {
      if (this.token !== null)
        return jwt_decode(this.token);
      else
        return null;
    },
    currentUserId: function() {
      if (this.currentToken !== null)
        return this.currentToken.sub;
      else
        return null;
    },
    loggedIn: function() {
      return this.token !== null;
    }
  },

  // Watchers for the app
  watch: {
    // Watcher for the token property
    token: function(newToken)
    {
      if (newToken !== null)
        localStorage.setItem('token', newToken);
      else
        localStorage.removeItem('token');

      this.$client.token = newToken;
    }
  },

  // Hook for when the app is created
  created: function()
  {
    if (localStorage.getItem('token') !== null)
      this.token = localStorage.getItem('token');
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


// Function to format bytes to a human-readable string
Vue.prototype.$formatBytes = function(bytes, decimals = 2)
{
  if (bytes === 0)
    return '0 bytes';

  const k = 1024;
  const dm = decimals < 0 ? 0 : decimals;
  const sizes = ['bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];

  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

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
