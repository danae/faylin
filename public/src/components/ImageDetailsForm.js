import Image from '../api/Image.js';


// Image details form component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
  },

  // The methods for the component
  methods: {
    // Event handler when the form is submitted
    onSubmit: async function()
    {
      // Patch the image
      this.patchImage();
    },

    // Patch the image
    patchImage: async function()
    {
      try
      {
        // Send a patch request
        const image = await this.image.patch();

        // Emit the success event
        this.$emit('image-patch-success', image);
      }
      catch (error)
      {
        // Emit the error event
        this.$emit('image-patch-error', error);
      }
    },

    // Delete the image
    deleteImage: async function()
    {
      try
      {
        // Send a delete request
        await this.image.delete();

        // Emit the success event
        this.$emit('image-delete-success');
      }
      catch (error)
      {
        // Emit the error event
        this.$emit('image-delete-error', error);
      }
    },
  },

  // The template for the component
  template: `
    <div class="image-details">
      <template v-if="image">
        <form id="image-details-form" @submit.prevent="onSubmit">
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
