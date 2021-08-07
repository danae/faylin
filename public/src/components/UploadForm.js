import UploadMixin from '../mixins/UploadMixin.js';


// Upload form component
export default {
  // The mixins for the component
  mixins: [UploadMixin],

  // The data for the component
  data: function() {
    return {
      // The upload form image as a data URL
      image: null,

      // The default upload form message
      defaultMessage: 'Upload or drag an image here or paste one from your clipboard',

      // The actual upload form message
      message: 'Upload or drag an image here or paste one from your clipboard',
    }
  },

  // Hook when the component is created
  created: function() {
    // Register upload event handlers
    this.$on('upload-start', this.onUploadStart.bind(this));
    this.$on('upload-end', this.onUploadEnd.bind(this));
    this.$on('upload-success', this.onUploadSuccess.bind(this));
    this.$on('upload-error', this.onUploadError.bind(this));

    // Register paste event handler
    window.addEventListener('paste', this.onFilePaste);

    // Set the upload form message
    this.message = this.defaultMessage;
  },

  // Hook when the component is destroyed
  destroyed: function() {
    // Unregister upload event handlers
    this.$off('upload-start', this.onUploadStart.bind(this));
    this.$off('upload-end', this.onUploadEnd.bind(this));
    this.$off('upload-success', this.onUploadSuccess.bind(this));
    this.$off('upload-error', this.onUploadError.bind(this));

    // Unregister paste event handler
    window.removeEventListener('paste', this.onFilePaste);
  },

  // The methods for the component
  methods: {
    // Event handler for when the upload starts
    onUploadStart: function(file) {
      // Set the form image and message
      this.image = URL.createObjectURL(file);
      this.message = this.$iconText('spinner fa-spin', 'Uploading image...');
    },

    // Event handler for when the upload ends
    onUploadEnd: function(file) {
      // Reset the form image and message
      URL.revokeObjectURL(this.image);

      this.image = null;
      this.message = this.defaultMessage;

      // Reset the file input
      this.$refs.fileInput.value = '';
    },

    // Event handler for when the upload is successful
    onUploadSuccess: function(image) {
      // Display a success message
      this.$displayMessage('Uploaded succesfully');

      // Redirect to the newly created image
      this.$router.push({name: 'imageView', params: {imageId: image.id}});
    },

    // Event handler for when the upload is unsuccessful
    onUploadError: function(error) {
      // Display an error message
      this.$displayError(error);
    },

    // Event handler when a file is selected using the file input
    onFileInput: async function(event) {
      // Upload the first file from the event target
      await this.$uploadFileInput(event.target);
    },

    // Event handler when a file is dropped
    onFileDrop: async function(event) {
      // Upload the data transfer from the event
      await this.$uploadDataTransfer(event.dataTransfer);
    },

    // Event handler when a file is pasted
    onFilePaste: async function(event) {
      // Upload the clipboard data from the event
      await this.$uploadDataTransfer(event.clipboardData);
    },
  },

  // The template for the component
  template: `
    <div class="upload-form">
      <div class="box is-primary has-text-centered" @dragenter.prevent @dragover.prevent @drop.prevent="onFileDrop">
        <div class="mb-3">
          <template v-if="image">
            <img class="is-uploading" :src="image">
          </template>

          <template v-else>
            <a class="is-borderless" @click.prevent="$refs.fileInput.click()">
              <b-icon pack="fas" icon="upload fa-3x" size="is-large"></b-icon>
            </a>
          </template>
        </div>

        <p class="has-text-primary" v-html="message"></p>
      </div>

      <input ref="fileInput" id="file" type="file" class="is-hidden" @change.prevent="onFileInput">
    </div>
  `
};
