// Home route component
export default {
  // The data for the route
  data: function()
  {
    return {
      // The users that will be viewed
      users: null,
    }
  },

  // Hook when the component is created
  created: async function()
  {
    try
    {
      // Get the images
      this.users = await this.$client.getUsers({sort: 'name'});
    }
    catch (error)
    {
      // Display the error message
      this.$displayErrorMessage(error.message);
    }
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
