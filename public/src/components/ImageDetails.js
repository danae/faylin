import Image from '../api/Image.js';


// Image details component
export default {
  // The properties for the component
  props: {
    // The image to reference in the component
    image: {type: Image},

    // Indicate if the client user is the owner of the image
    owner: {type: Boolean, default: false},
  },

  // The template for the component
  template: `
    <div class="image-details">
      <template v-if="image">
        <div class="columns">
          <div class="column is-8">
            <a :href="image.downloadUrl">
              <b-image class="image-details-image mx-0 mb-6" :src="image.downloadUrl" :alt="image.name"></b-image>
            </a>
          </div>

          <div class="column is-4 content">
            <image-details-sidebar :image="image" :owner="owner"></image-details-sidebar>
          </div>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
