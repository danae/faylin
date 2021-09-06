import Collection from '../api/Collection.js';


// Collection details edit panel component
export default {
  // The properties for the component
  props: {
    // The collection that is referenced in the component
    collection: {type: Collection},
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

      // Emit the close event
      this.$emit('close');
    },
  },

  // The template for the component
  template: `
    <div class="collection-edit-panel">
      <template v-if="collection">
        <form @submit.prevent="patchCollection()">
          <div class="box is-panel">
            <b-field label="Title" label-for="title" custom-class="is-small">
              <b-input v-model="collection.title" type="text" maxlength="64" :has-counter="false" name="title"></b-input>
            </b-field>

            <b-field label="Description" label-for="description" custom-class="is-small">
              <b-input v-model="collection.description" type="textarea" maxlength="256" :has-counter="false" name="description"></b-input>
            </b-field>

            <b-field label="Visibility settings" custom-class="is-small">
              <b-switch v-model="collection.public">Listed publicly</b-switch>
            </b-field>

            <b-button type="is-primary" expanded icon-left="save" icon-pack="fas" class="mb-2" @click="patchCollection()">
              Save collection
            </b-button>
          </div>
        </form>
      </template>

      <template v-else>
        <b-loading active :is-full-page="false"></b-loading>
      </template>
    </div>
  `
};
