// Image view route component
export default {
  // The data for the route
  data: function() {
    return {
      // The user that will be viewed
      user: null,
    }
  },

  // Hook when the component is created
  created: async function() {
    // Get the user
    this.user = await this.$root.client.getUser(this.$route.params.userId);
  },

  // The template for the route
  template: `
    <div class="user-view-page">
      <section class="section content">
        <user-details :user="user"></user-details>
      </section>
    </div>
  `
};
