// Collection details route component
export default {
  // The data for the route
  data: function() {
    return {
      // The collection that will be viewed
      collection: null,
    }
  },

  // The computed data for the route
  computed: {
    // Indicate if the client user is the owner of the collection
    owner: function() {
      return this.collection && this.$root.clientUser && this.$root.clientUser.id == this.collection.user.id;
    },
  },

  // Hook when the component is created
  created: async function() {
    // Set the document title
    document.title = `Collection – fayl.in`;

    // Get the collection
    this.collection = await this.$root.client.getCollection(this.$route.params.collectionId);
    document.title = `${this.collection.name} – fayl.in`;
  },

  // The template for the route
  template: `
    <div class="collection-details-page">
      <hr class="bar">

      <template v-if="collection">
        <collection-details :collection="collection" :owner="owner"></collection-details>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
