// Home route component
export default {
  // The data for the route
  data: function()
  {
    return {
      // The images that will be viewed
      images: null,
    }
  },

  // Hook when the component is created
  created: async function()
  {
    try
    {
      // Get the images
      this.images = await this.$client.getImages({perPage: 30});
    }
    catch (error)
    {
      // Display the error message
      this.$displayErrorMessage(error.message);
    }
  },

  // The methods for the route
  methods: {
    // Event handler when the upload has succeeded
    onUploadSuccess: function(image)
    {
      // Display a success message
      this.$displayMessage('Uploaded succesfully');

      // Redirect to the newly created image
      this.$router.push({name: 'imageView', params: {imageId: image.id}});
    },

    // Event handler when something went wrong
    onError: function(error)
    {
      // Display an error message
      this.$displayErrorMessage(error.message);
    },
  },

  // The template for the route
  template: `
    <div id="home-page">
      <template v-if="$root.loggedIn">
        <section class="section content">
          <upload-form @upload-success="onUploadSuccess" @upload-error="onError"></upload-form>
        </section>
      </template>

      <section class="section content">
        <image-thumbnail-list :images="images"></image-thumbnail-list>
      </section>
    </div>
  `
};
