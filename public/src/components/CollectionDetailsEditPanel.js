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
          <div class="panel is-primary">
            <p class="panel-heading">Edit collection</p>

            <div class="panel-block is-form">
              <b-field label="Title" label-for="title" custom-class="is-small">
                <b-input v-model="collection.title" type="text" name="title"></b-input>
              </b-field>

              <b-field label="Description" label-for="description" custom-class="is-small">
                <b-input v-model="collection.description" type="textarea" name="description"></b-input>
              </b-field>
            </div>

            <div class="panel-block is-form">
              <p class="menu-label">
                <span class="icon-text">
                  <b-icon icon="eye" pack="fas"></b-icon>
                  <span>Visibility settings</span>
                </span>
              </p>

              <b-field>
                <b-switch v-model="collection.public" size="is-small">Public</b-switch>
              </b-field>
            </div>

            <a class="panel-block" @click="patchCollection()">
              <b-icon icon="save" pack="fas" class="panel-icon"></b-icon> Save collection
            </a>
          </div>
        </form>
      </template>

      <template v-else>
        <b-loading active :is-full-page="false"></b-loading>
      </template>
    </div>
  `
};
