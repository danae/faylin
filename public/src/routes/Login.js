// Login route component
export default {
  // The methods for the route
  methods: {
    // Event handler when the login has succeeded
    onLoginSuccess: function() {
      // Display a success message
      this.$displayMessage('Logged in succesfully');

      // Redirect to the page specified by the query, or the home page otherwise
      let query = new URLSearchParams(window.location.search);
      if (query.has('redirect'))
        this.$router.push(query.get('redirect'));
      else
        this.$router.push('/');
    },
  },

  // The template for the route
  template: `
    <div id="login-page">
      <section class="section content">
        <login-form @login-success="onLoginSuccess"></login-form>
      </section>
    </div>
  `
};
