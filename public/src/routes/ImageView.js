// Image view route component
export default {
  // The data for the route
  data: function()
  {
    return {
      // The image that will be viewed
      image: null,
    }
  },

  // Hook when the component is created
  created: async function()
  {
    try
    {
      // Get the image
      this.image = await this.$client.getImage(this.$route.params.imageId)
    }
    catch (error)
    {
      // Display the error message
      this.$displayErrorMessage(error.message);
    }
  },

  // The template for the route
  template: `
    <div id="image-view-page">
      <section class="section content">
        <image-details :image="image"></image-details>
      </section>
    </div>
  `
};
