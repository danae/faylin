// Home route component
export default {
  // The data for the route
  data: function() {
    return {
      // The users that will be viewed
      users: null,
    }
  },

  // Hook when the component is created
  created: async function() {
    // Set the document title
    document.title = `Browse – fayl.in`;

    // Get the users
    this.users = await this.$root.client.getUsers();
  },

  // The template for the route
  template: `
    <div class="browse-page">
      <hr class="bar">

      <div class="container">
        <user-thumbnail-list :users="users"></user-thumbnail-list>
      </div>
    </div>
  `
};
