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
      const image = await this.$root.client.patchImage(this.image.id, {
        name: this.image.name,
        description: this.image.description,
        tags: this.image.tags,
        public: this.image.public,
        nsfw: this.image.nsfw,
      });

      // Display a success message
      this.$displayMessage('Image updated succesfully');

      // Update the image
      this.image.update(image);

      // Emit the close event
      this.$emit('close');
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
    <div class="image-edit-panel">
      <template v-if="image">
        <form @submit.prevent="patchImage()">
          <div class="panel is-primary">
            <p class="panel-heading">Edit image</p>

            <div class="panel-block is-form">
              <b-field label="Name" label-for="name" label-position="on-border">
                <b-input v-model="image.name" type="text" name="name"></b-input>
              </b-field>

              <b-field label="Description" label-for="description" label-position="on-border">
                <b-input v-model="image.description" type="textarea" name="description"></b-input>
              </b-field>

              <b-field label="Tags" label-for="tags" label-position="on-border">
                <b-taginput v-model="image.tags" maxlength="32" maxtags="10" :has-counter="false" type="is-light" close-type="is-light" attached></b-taginput>
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
                <b-switch v-model="image.public" size="is-small">Public</b-switch>
              </b-field>

              <b-field>
                <b-switch v-model="image.nsfw" size="is-small">Mature content</b-switch>
              </b-field>
            </div>

            <a class="panel-block" @click="patchImage()">
              <b-icon icon="save" pack="fas" class="panel-icon"></b-icon> Save image
            </a>

            <a class="panel-block" @click="replaceImage()">
              <b-icon icon="upload" pack="fas" class="panel-icon"></b-icon> Replace image
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
