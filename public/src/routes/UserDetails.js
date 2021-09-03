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
    // Set the document title
    document.title = `User – fayl.in`;

    // Get the user
    this.user = await this.$root.client.getUser(this.$route.params.userId);
    document.title = `${this.user.title} – fayl.in`;
  },

  // The template for the route
  template: `
    <div class="user-view-page">
      <user-details :user="user"></user-details>
    </div>
  `
};
