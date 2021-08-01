import Image from '../api/Image.js';


// Image thumbnail component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
    displayName: {type: Boolean, default: true},
    displayUser: {type: Boolean, default: true},
  },

  // The template for the component
  template: `
    <div class="image-thumbnail">
      <template v-if="image">
        <router-link :to="{name: 'imageView', params: {imageId: image.id}}">
          <b-image class="mx-0 my-1" :src="image.downloadUrl" :alt="image.name"></b-image>
        </router-link>

        <template v-if="displayName">
          <h4 class="image-thumbnail-name mb-0">{{ image.name }}</h4>
        </template>

        <template v-if="displayUser">
          <p class="image-thumbnail-user mb-0">by {{ image.user.name }}</p>
        </template>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
