import Image from '../api/Image.js';


// Image details component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
  },

  // The methods for the component
  methods: {
    // Event handler when the image patch was successful
    onEditPatchSuccess: function(image) {
      // Display a success message
      this.$displayMessage('Image updated succesfully');
    },

    // Event handler when the image deletion was successful
    onEditDeleteSuccess: function() {
      // Display a success message
      this.$displayMessage('Image deleted succesfully');

      // Redirect to the previous page
      this.$router.back();
    },

    // Event handler when the image edit was unsuccessful
    onEditError: function(error) {
      // Display the error
      this.$displayError(error);
    },
  },

  // The template for the component
  template: `
    <div class="image-details">
      <template v-if="image">
        <div class="columns">
          <div class="column is-8">
            <b-image class="mx-0 mb-6" :src="image.downloadUrl" :alt="image.name"></b-image>
          </div>

          <div class="column is-4">
            <h2 class="image-details-name mb-0">{{ image.name }}</h2>
            <p class="image-details-user">by <router-link :to="{name: 'userView', params: {userId: image.user.id }}">{{ image.user.name }}</router-link></p>

            <image-details-share-panel :image="image" class="mb-4"></image-details-share-panel>

            <template v-if="$root.clientUser && $root.clientUser.id == image.user.id">
              <image-details-edit-panel :image="image" class="mb-4" @edit-patch-success="onEditPatchSuccess" @edit-delete-success="onEditDeleteSuccess" @edit-error="onEditError"></image-details-edit-panel>
            </template>
          </div>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
