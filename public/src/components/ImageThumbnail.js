import Image from '../api/Image.js';


// Image thumbnail component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
    displayName: {type: Boolean, default: true},
    displayUserName: {type: Boolean, default: true},
  },

  // The template for the component
  template: `
    <div class="image-thumbnail content">
      <template v-if="image">
        <router-link :to="{name: 'image', params: {imageId: image.id}}">
          <b-image class="image-thumbnail-image" :src="image.thumbnailUrl" :alt="image.name"></b-image>
        </router-link>

        <template v-if="displayName">
          <h4 class="image-thumbnail-name mb-0">{{ image.name }}</h4>
        </template>

        <template v-if="displayUserName">
          <p class="image-thumbnail-user-name mb-0">by <router-link :to="{name: 'user', params: {userId: image.user.id}}">{{ image.user.name }}</router-link></p>
        </template>
      </template>

      <template v-else>
        <b-loading active :is-full-page="false"></b-loading>
      </template>
    </div>
  `
};
