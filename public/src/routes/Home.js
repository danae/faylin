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
        <section class="section content">
          <upload-form></upload-form>
        </section>
      </template>

      <section class="section content">
        <image-thumbnail-list :images="images"></image-thumbnail-list>
      </section>
    </div>
  `
};
