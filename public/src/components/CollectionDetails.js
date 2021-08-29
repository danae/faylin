import Collection from '../api/Collection.js';


// Collection details component
export default {
  // The properties for the component
  props: {
    // The collection that is referenced by the component
    collection: {type: Collection},

    // Indicate if the client user is the owner of the image
    owner: {type: Boolean, default: false},
  },

  // The data for the component
  data: function() {
    return {
      // Indicates if the collection is currently being edited
      editing: false,
    }
  },

  // The methods for the component
  methods: {
    // Share the collection
    shareCollection: async function() {
      // Share the collection
      await navigator.share({url: this.$route.fullPath, title: this.collection.name, text: this.collection.description});
    },

    // Delete the collection
    deleteCollection: async function() {
      // Show the confirmation dialog
      this.$buefy.dialog.confirm({
        type: 'is-danger',
        hasIcon: true,
        icon: 'trash-alt',
        iconPack: 'fas',
        trapFocus: true,
        message: `Are you sure you want to delete the collection <b>${this.collection.name}</b>? All associated data and links to the collection will stop working forever, which is a long time!`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
        onConfirm: await this.deleteCollectionConfirmed.bind(this),
      });
    },

    // Delete the collection after confirmation
    deleteCollectionConfirmed: async function() {
      // Send a delete request
      await this.$root.client.deleteCollection(this.collection.id);

      // Display a success message
      this.$displayMessage('Collection deleted succesfully');

      // Redirect to the previous page
      this.$router.back();
    },

    // Toggle the editing state
    toggleEditing: function() {
      this.editing = !this.editing;
    },
  },

  // The template for the component
  template: `
    <div class="collection-details">
      <template v-if="collection">
        <div class="container">
          <section class="section">
            <div class="columns">
              <div class="column is-8">
                <image-thumbnail-list :images="collection.images"></image-thumbnail-list>
              </div>

              <div class="column is-4 content">
                <collection-details-buttons :collection="collection" :owner="owner" :editing="editing" class="mb-3" @share="shareCollection()" @edit="toggleEditing()" @delete="deleteCollection"></collection-details-buttons>

                <template v-if="editing">
                  <collection-details-edit-panel :collection="collection" @close="toggleEditing()"></collection-details-edit-panel>
                </template>

                <template v-else>
                  <h2 class="image-details-name mb-0">{{ collection.name }}</h2>
                  <p class="image-details-user-name">by <router-link :to="{name: 'user', params: {userId: collection.user.id }}">{{ collection.user.name }}</router-link></p>

                  <template v-if="collection.description">
                    <p>{{ collection.description }}</p>
                  </template>
                </template>
              </div>
            </div>
          </section>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};