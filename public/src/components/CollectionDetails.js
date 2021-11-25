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

    // Edit the collection
    editCollection: function() {
      this.editing = true;
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
        message: `Are you sure you want to delete the collection <b>${this.collection.title}</b>? All associated data and links to the collection will stop working forever, which is a long time!`,
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
  },

  // The template for the component
  template: `
    <div class="collection-details">
      <template v-if="collection">
        <div class="container">
          <section class="section">
            <div class="columns">
              <div class="column is-8">
                <image-thumbnail-list :images="collection.images" responsiveSmall></image-thumbnail-list>
              </div>

              <div class="column is-4 content">
                <collection-details-buttons :collection="collection" :owner="owner" :editing="editing" class="mb-3" @share="shareCollection" @edit="editCollection" @delete="deleteCollection"></collection-details-buttons>

                <b-modal v-model="editing" :width="600">
                  <collection-details-edit-modal :collection="collection"></collection-details-edit-modal>
                </b-modal>

                <div class="media is-align-items-center mb-4">
                  <template v-if="collection.user.avatarUrl">
                    <div class="media-left mr-3">
                      <router-link :to="{name: 'user', params: {userName: collection.user.name}}">
                        <b-image class="avatar is-48x48" :src="collection.user.avatarUrl" :alt="collection.user.title"></b-image>
                      </router-link>
                    </div>
                  </template>

                  <div class="media-content">
                    <p class="is-size-4 is-family-secondary has-text-weight-bold mb-0">{{ collection.title }}</p>
                    <p class="is-size-6 mb-0">by <router-link :to="{name: 'user', params: {userName: collection.user.name}}">{{ collection.user.title }}</router-link></p>
                  </div>
                </div>

                <template v-if="collection.description">
                  <p>{{ collection.description }}</p>
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
