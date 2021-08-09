import Image from '../api/Image.js';
import UploadMixin from '../mixins/UploadMixin.js';


// Image details edit panel component
export default {
  // The mixins for the component
  mixins: [UploadMixin],

  // The properties for the component
  props: {
    // The image to reference in the component
    image: {type: Image},
  },

  // Hook when the component is created
  created: function() {
    // Set the referenced image
    this.image = this.image;

    // Register upload event handlers
    this.$on('upload-start', this.onUploadStart.bind(this));
    this.$on('upload-end', this.onUploadEnd.bind(this));
    this.$on('upload-success', this.onUploadSuccess.bind(this));
    this.$on('upload-error', this.onUploadError.bind(this));
  },

  // Hook when the component is destroyed
  destroyed: function() {
    // Unregister upload event handlers
    this.$off('upload-start');
    this.$off('upload-end');
    this.$off('upload-success');
    this.$off('upload-error');
  },

  // The methods for the component
  methods: {
    // Patch the image
    patchImage: async function() {
      // Send a patch request
      const image = await this.$root.client.patchImage(this.image.id, {name: this.image.name});

      // Display a success message
      this.$displayMessage('Image updated succesfully');

      // Update the image
      this.image.update(image);
    },

    // Replace the image
    replaceImage: function() {
      // Click the file input to select a file
      this.$file.click();
    },

    // Delete the image
    deleteImage: async function() {
      // Show the confirmation dialog
      this.$buefy.dialog.confirm({
        type: 'is-danger',
        hasIcon: true,
        icon: 'trash-alt',
        iconPack: 'fas',
        trapFocus: true,
        message: `Are you sure you want to delete <b>${this.image.name}</b>? All associated data and links to the image will stop working forever, which is a long time!`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
        onConfirm: await this.deleteImageConfirmed.bind(this),
      });
    },

    // Delete the image after confirmation
    deleteImageConfirmed: async function() {
      // Send a delete request
      await this.$root.client.deleteImage(this.image.id);

      // Display a success message
      this.$displayMessage('Image deleted succesfully');

      // Redirect to the previous page
      this.$router.back();
    },

    // Event handler for when the upload starts
    onUploadStart: function(file) {

    },

    // Event handler for when the upload ends
    onUploadEnd: function(file) {

    },

    // Event handler for when the upload is succesful
    onUploadSuccess: function(image) {
      // Display a success message
      this.$displayMessage('Image replaced succesfully');

      // Update the image
      this.image.update(image);
    },

    // Event handler for when the upload is unsuccessful
    onUploadError: function(error) {
      // Display an error message
      this.$displayError(error);
    },
  },

  // The template for the component
  template: `
    <div class="image-edit-panel">
      <template v-if="image">
        <form @submit.prevent="patchImage()">
          <div class="panel is-primary">
            <p class="panel-heading">Edit image</p>

            <div class="panel-block is-form">
              <b-field label="Name" label-for="name" label-position="on-border">
                <b-input v-model="image.name" type="text" name="name" id="name"></b-input>
              </b-field>
            </div>

            <a class="panel-block" @click="patchImage()">
              <b-icon icon="save" pack="fas" class="panel-icon"></b-icon> Save
            </a>

            <a class="panel-block" @click="replaceImage()">
              <b-icon icon="upload" pack="fas" class="panel-icon"></b-icon> Replace
            </a>

            <a class="panel-block has-text-danger" @click="deleteImage()">
              <b-icon icon="trash-alt" pack="fas" type="is-danger" class="panel-icon"></b-icon> Delete
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
