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
    document.title = `Directory – fayl.in`;

    // Get the users
    this.users = await this.$root.client.getUsers({sort: 'name'});
  },

  // The template for the route
  template: `
    <div class="home-page">
      <section class="section content">
        <user-thumbnail-list :users="users"></user-thumbnail-list>
      </section>
    </div>
  `
};
