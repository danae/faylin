// Home route component
export default {
  // The data for the route
  data: function() {
    return {
      // The images that will be viewed
      images: null,
    }
  },

  // Hook when the component is created
  created: async function() {
    // Get the images
    this.images = await this.$root.$getImages({perPage: 30});
  },

  // The methods for the route
  methods: {
    // Event handler when the upload has succeeded
    onUploadImageSuccess: function(image) {
      // Display a success message
      this.$displayMessage('Uploaded succesfully');

      // Redirect to the newly created image
      this.$router.push({name: 'imageView', params: {imageId: image.id}});
    },
  },

  // The template for the route
  template: `
    <div id="home-page">
      <template v-if="clientLoggedIn">
        <section class="section content">
          <upload-form @upload-image-success="onUploadImageSuccess"></upload-form>
        </section>
      </template>

      <section class="section content">
        <image-thumbnail-list :images="images"></image-thumbnail-list>
      </section>
    </div>
  `
};
