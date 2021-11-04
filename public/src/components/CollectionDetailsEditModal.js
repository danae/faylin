import Collection from '../api/Collection.js';


// Collection details edit modal component
export default {
  // The properties for the component
  props: {
    // The collection that is referenced in the component
    collection: {type: Collection},

    // Toggle if the component modal is active
    active: {type: Boolean},
  },

  // The methods for the component
  methods: {
    // Patch the collection
    patchCollection: async function() {
      // Send a patch request
      const collection = await this.$root.client.patchCollection(this.collection.id, {
        title: this.collection.title,
        description: this.collection.description,
        public: this.collection.public,
      });

      // Display a success message
      this.$displayMessage('Collection updated succesfully');

      // Update the collection
      this.collection.update(collection);

      // Close the modal
      this.close();
    },

    // Close the modal
    close: function() {
      this.$parent.close();
    },
  },

  // The template for the component
  template: `
    <div class="collection-edit-panel">
      <template v-if="collection">
        <form @submit.prevent="patchCollection()">
          <div class="modal-card" style="width: auto;">
            <section class="modal-card-body">
              <b-field label="Title" label-for="title" custom-class="is-small">
                <b-input v-model="collection.title" type="text" maxlength="64" :has-counter="false" name="title"></b-input>
              </b-field>

              <b-field label="Description" label-for="description" custom-class="is-small">
                <b-input v-model="collection.description" type="textarea" maxlength="256" :has-counter="false" name="description"></b-input>
              </b-field>

              <b-field label="Visibility settings" custom-class="is-small">
                <b-switch v-model="collection.public">Listed publicly</b-switch>
              </b-field>
            </section>

            <footer class="modal-card-foot is-justify-content-flex-end">
              <b-button type="is-white" @click="close">
                Cancel
              </b-button>

              <b-button type="is-primary" @click="patchCollection">
                Save collection
              </b-button>
            </footer>
          </div>
        </form>
      </template>
    </div>
  `
};
