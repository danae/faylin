// Image view route component
export default {
  // The data for the route
  data: function() {
    return {
      // The image that will be viewed
      image: null,
    }
  },

  // Hook when the component is created
  created: async function() {
    // Get the image
    this.image = await this.$root.client.getImage(this.$route.params.imageId);
  },

  // The template for the route
  template: `
    <div class="image-view-page">
      <section class="section content">
        <image-details :image="image"></image-details>
      </section>
    </div>
  `
};
