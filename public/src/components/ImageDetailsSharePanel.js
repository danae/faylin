import Image from '../api/Image.js';


// Image details share panel component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
  },

  // The methods for the component
  methods: {
    // Return if the Web share API is available
    canShareImage: function() {
      return navigator.share !== undefined;
    },

    // Share the image
    shareImage: async function() {
      // Share the image
      await navigator.share({url: this.image.downloadUrl, title: this.image.name});
    },

    // Copy the image link to the clipboard
    copyImage: async function(template = null) {
      // Check for the clipboard permission
      const permission = await navigator.permissions.query({name: "clipboard-write"});
      if (permission.state == "granted" || permission.state == "prompt")
      {
        // Write the link to the clipboard
        const text = this.image.downloadUrl;
        if (template !== null)
          text = template(text);
        await navigator.clipboard.writeText(text);

        // Display a success message
        this.$displayMessage('Image link copied succesfully');
      }
      else
      {
        // Display an error message
        this.$displayMessage('No permission to use the clipboard', 'is-danger', 5000);
      }
    },
  },

  // The template for the component
  template: `
    <div class="image-details-share-panel">
      <template v-if="image">
        <div class="panel is-primary">
          <p class="panel-heading">Share image</p>

          <template v-if="canShareImage()">
            <a class="panel-block" @click="shareImage()">
              <b-icon icon="share-alt" pack="fas" class="panel-icon"></b-icon> Share
            </a>
          </template>

          <a class="panel-block" :href="image.downloadUrl + '?dl=1'">
            <b-icon icon="download" pack="fas" class="panel-icon"></b-icon> Download
          </a>

          <a class="panel-block" @click="copyImage()">
            <b-icon icon="link" pack="fas" class="panel-icon"></b-icon> Copy link
          </a>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
