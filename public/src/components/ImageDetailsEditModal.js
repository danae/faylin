import Image from '../api/Image.js';
import UploadMixin from '../mixins/UploadMixin.js';


// Image details edit modal component
export default {
  // The mixins for the component
  mixins: [UploadMixin],

  // The properties for the component
  props: {
    // The image that is referenced in the component
    image: {type: Image},

    // Toggle if the component modal is active
    active: {type: Boolean, default: false},
  },

  // Hook when the component is created
  created: function() {
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
      const image = await this.$root.client.patchImage(this.image.id, {
        title: this.image.title,
        description: this.image.description,
        public: this.image.public,
        nsfw: this.image.nsfw,
      });

      // Display a success message
      this.$displayMessage('Image updated succesfully');

      // Update the image
      this.image.update(image);

      // Close the modal
      this.close();
    },

    // Close the modal
    close: function() {
      this.$parent.close();
    },

    // Replace the image
    replaceImage: function() {
      // Click the file input to select a file
      this.$file.click();
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
    <div class="image-edit-modal">
      <template v-if="image">
        <form @submit.prevent="patchImage()">
          <div class="modal-card" style="width: auto;">
            <section class="modal-card-body">
              <b-field label="Title" label-for="title" custom-class="is-small">
                <b-input v-model="image.title" type="text" maxlength="64" :has-counter="false" name="title"></b-input>
              </b-field>

              <b-field label="Description" label-for="description" custom-class="is-small">
                <b-input v-model="image.description" type="textarea" maxlength="256" :has-counter="false" name="description"></b-input>
              </b-field>

              <b-field label="Visibility settings" custom-class="is-small" class="mb-1">
                <b-switch v-model="image.public">Listed publicly</b-switch>
              </b-field>

              <b-field>
                <b-switch v-model="image.nsfw">Mature content</b-switch>
              </b-field>

              <b-field label="Content" custom-class="is-small" class="mb-1">
                <b-button type="is-primary" outlined icon-left="upload" icon-pack="fas" @click="replaceImage()">
                  Replace image
                </b-button>
              </b-field>
            </section>

            <footer class="modal-card-foot is-justify-content-flex-end">
              <b-button type="is-white" @click="close">
                Cancel
              </b-button>

              <b-button type="is-primary" @click="patchImage">
                Save image
              </b-button>
            </footer>
          </div>
        </form>
      </template>
    </div>
  `
};
