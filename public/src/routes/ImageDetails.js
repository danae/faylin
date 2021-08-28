// Image view route component
export default {
  // The data for the route
  data: function() {
    return {
      // The image that will be viewed
      image: null,

      // The collections the image can be added to
      collections: [],
    }
  },

  // The computed data for the route
  computed: {
    // Indicate if the client user is the owner of the image
    owner: function() {
      return this.image && this.$root.clientUser && this.$root.clientUser.id == this.image.user.id;
    },
  },

  // Hook when the component is created
  created: async function() {
    // Set the document title
    document.title = `Image – fayl.in`;

    // Get the image
    this.image = await this.$root.client.getImage(this.$route.params.imageId);
    document.title = `${this.image.name} by ${this.image.user.name} – fayl.in`;

    // Get the collection the image can be added to
    if (this.$root.clientUser)
      this.collections = await this.$root.client.getAuthorizedUserCollections();
  },

  // The template for the route
  template: `
    <div class="image-details-page">
      <template v-if="image">
        <section class="section">
          <image-details :image="image" :collections="collections" :owner="owner"></image-details>
        </section>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
