import Image from '../api/Image.js';


// Image details form component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
  },

  // The methods for the component
  methods: {
    // Patch the image
    patchImage: async function() {
      // Send a patch request
      await this.$root.$patchImage(this.image.id);
    },

    // Delete the image
    deleteImage: async function() {
      // Send a delete request
      await this.$root.$deleteImage(this.image.id);
    },
  },

  // The template for the component
  template: `
    <div class="image-details">
      <template v-if="image">
        <form id="image-details-form" @submit.prevent="patchImage">
          <h4>Image details</h4>

          <b-field label="Name" label-position="on-border">
            <b-input v-model="image.name" type="text" name="name" id="name"></b-input>
          </b-field>

          <div class="buttons">
            <b-button type="is-light" icon-pack="fas" icon-left="save" native-type="submit">Save</b-button>
            <b-button type="is-danger" icon-pack="fas" icon-left="trash-alt" @click="deleteImage">Delete</b-button>
          </div>
        </form>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
