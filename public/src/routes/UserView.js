// Image view route component
export default {
  // The data for the route
  data: function()
  {
    return {
      // The user that will be viewed
      user: null,
    }
  },

  // Hook when the component is created
  created: async function()
  {
    try
    {
      // Get the user
      this.user = await this.$client.getUser(this.$route.params.userId)
    }
    catch (error)
    {
      // Display the error message
      this.$displayErrorMessage(error.message);
    }
  },

  // The template for the route
  template: `
    <div id="user-view-page">
      <section class="section content">
        <user-details :user="user"></user-details>
      </section>
    </div>
  `
};
