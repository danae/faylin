import Image from '../api/Image.js';


// Image details component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
  },

  // The methods for the component
  methods: {
    // Copy the image link to the clipboard
    copyLink: async function() {
      // Check for the clipboard permission
      const permission = await navigator.permissions.query({name: "clipboard-write"});
      if (permission.state == "granted" || permission.state == "prompt")
      {
        // Write the link to the clipboard
        await navigator.clipboard.writeText(this.image.downloadUrl);

        // Display a success message
        this.$displayMessage('Link copied succesfully');
      }
      else
      {
        // Display an error message
        this.$displayErrorMessage('No permission to use the clipboard');
      }
    },

    // Callback for when the image patch has succeeded
    onImagePatchSuccess: function(image) {
      // Display a success message
      this.$displayMessage('Image updated succesfully');
    },

    // Callback for when the image delete has succeeded
    onImageDeleteSuccess: function() {
      // Display a success message
      this.$displayMessage('Image deleted succesfully');

      // Redirect to the previous page
      this.$router.back();
    },
  },

  // The template for the component
  template: `
    <div class="image-details">
      <template v-if="image">
        <div class="columns">
          <div class="column is-7">
            <a :href="image.downloadUrl">
              <b-image class="mx-0 mb-6" :src="image.downloadUrl" :alt="image.name"></b-image>
            </a>
          </div>

          <div class="column is-5">
            <h2 class="image-details-name mb-0">{{ image.name }}</h2>
            <p class="image-details-user">by <router-link :to="{name: 'userView', params: {userId: image.user.id }}">{{ image.user.name }}</router-link></p>

            <div class="buttons">
              <b-button type="is-light" tag="a" :href="image.downloadUrl + '?dl=1'" icon-pack="fas" icon-left="download">Download</b-button>
              <b-button type="is-light" icon-pack="fas" icon-left="link" @click="copyLink">Copy link</b-button>
            </div>

            <template v-if="image.user.id == $root.currentUserId">
              <image-details-form :image="image" class="mt-6" @image-patch-success="onImagePatchSuccess" @image-patch-error="onError" @image-delete-success="onImageDeleteSuccess" @on-image-delete-error="onError"></image-details-form>
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
