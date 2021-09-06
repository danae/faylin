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
    // Set the document title
    document.title = `Home – fayl.in`;

    // Get the images
    this.images = await this.$root.client.getImages({perPage: 30});
  },

  // The template for the route
  template: `
    <div class="home-page">
      <template v-if="$root.clientLoggedIn">
        <div class="hero is-primary mb-4">
          <div class="hero-body container">
            <upload-form></upload-form>
          </div>
        </div>
      </template>

      <template v-else>
        <hr class="bar">
      </template>

      <div class="container">
        <section class="section">
          <image-thumbnail-list :images="images" responsiveLarge></image-thumbnail-list>
        </section>
      </div>
    </div>
  `
};
