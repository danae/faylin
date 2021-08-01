// Login route component
export default {
  // The methods for the route
  methods: {
    // Callback for when the login has succeeded
    onLoginSuccess: function(token)
    {
      // Set the token
      this.$root.token = token;

      // Display a success message
      this.$displayMessage('Logged in succesfully');

      // Redirect to the next page
      let query = new URLSearchParams(window.location.search);
      if (query.has('redirect'))
        this.$router.replace(query.get('redirect'));
      else
        this.$router.replace('/');
    },

    // Callback for when the login has failed
    onLoginError: function(error)
    {
      // Display an error message
      this.$displayErrorMessage(error.message);
    },
  },

  // The template for the route
  template: `
    <div id="login-page">
      <section class="section content">
        <login-form @login-success="onLoginSuccess" @login-error="onLoginError"></login-form>
      </section>
    </div>
  `
};
