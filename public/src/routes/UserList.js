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
    // Get the users
    this.users = await this.$root.$getUsers({sort: 'name'});
  },

  // The template for the route
  template: `
    <div id="home-page">
      <section class="section content">
        <user-thumbnail-list :users="users"></user-thumbnail-list>
      </section>
    </div>
  `
};
