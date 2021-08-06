import Image from '../api/Image.js';


// Image details edit panel component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
  },

  // The methods for the component
  methods: {
    // Patch the image
    patchImage: function() {
      // Send a patch request
      this.$root.client.patchImage(this.image.id, {name: this.image.name})
        .then(image => this.$emit('edit-patch-success', image))
        .catch(error => this.$emit('edit-error', error));
    },

    // Ask for comfirmation to delete the image
    deleteImageComfirm: function() {
      // Show the confirmation dialog
      this.$buefy.dialog.confirm({
        type: 'is-danger',
        hasIcon: true,
        icon: 'trash-alt',
        iconPack: 'fas',
        message: `Are you sure you want to delete <b>${this.image.name}</b>? All associated data and links to the image will stop working forever, which is a long time!`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
        onConfirm: this.deleteImage.bind(this),
      });
    },

    // Delete the image
    deleteImage: async function() {
      // Send a delete request
      this.$root.client.deleteImage(this.image.id)
        .then(image => this.$emit('edit-delete-success', image))
        .catch(error => this.$emit('edit-error', error));
    },
  },

  // The template for the component
  template: `
    <div class="image-details-edit-panel">
      <template v-if="image">
        <form @submit.prevent="patchImage()">
          <div class="panel is-dark">
            <p class="panel-heading">Edit image</p>

            <div class="panel-block is-form">
              <b-field label="Name" label-for="name" label-position="on-border">
                <b-input v-model="image.name" type="text" name="name" id="name"></b-input>
              </b-field>
            </div>

            <a class="panel-block" @click="patchImage()">
              <b-icon icon="save" pack="fas" class="panel-icon"></b-icon> Save
            </a>
            <a class="panel-block">
              <b-icon icon="upload" pack="fas" class="panel-icon"></b-icon> Replace
            </a>
            <a class="panel-block has-text-danger" @click="deleteImageComfirm()">
              <b-icon icon="trash-alt" pack="fas" type="is-danger" class="panel-icon"></b-icon> Delete
            </a>
          </div>
        </form>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
