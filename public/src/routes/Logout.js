// Logout route component
export default {
  // Hook when the route has been created
  created: function() {
    // Set the document title
    document.title = `Log out – fayl.in`;

    // Send a logout request
    this.$logout();

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
    <div class="logout-page">
      <b-loading active></b-loading>
    </div>
  `
};
