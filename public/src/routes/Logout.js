// Logout route component
export default {
  // Hook when the route has been created
  created: async function() {
    // Send a logout request
    await this.$logout();

    // Display a success message
    this.$displayMessage('Logged out succesfully');

    // Redirect to the page specified by the query, or the home page otherwise
    let query = new URLSearchParams(window.location.search);
    if (query.has('redirect'))
      this.$router.replace(query.get('redirect'));
    else
      this.$router.replace({name: 'home'});
  },

  // The template for the route
  template: `
    <div id="logout-page">
      <b-loading active></b-loading>
    </div>
  `
};
