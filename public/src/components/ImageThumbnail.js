import Image from '../api/Image.js';


// Image thumbnail component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
  },

  // The template for the component
  template: `
    <div class="thumbnail content">
      <template v-if="image">
        <router-link :to="{name: 'image', params: {imageId: image.id}}">
          <div class="is-relative">
            <b-image class="thumbnail-image" :src="image.thumbnailUrl" :alt="image.title" :class="{'is-nsfw': image.nsfw}"></b-image>

            <template v-if="image.nsfw">
              <div class="thumbnail-nsfw">
                <b-icon icon="minus-circle" pack="fas" size="is-large"></b-icon>
              </div>
            </template>

            <div class="thumbnail-cover">
              <div class="media is-align-items-center mb-4">
                <template v-if="image.user.avatarUrl">
                  <div class="media-left mr-2">
                    <b-image class="avatar is-32x32" :src="image.user.avatarUrl" :alt="image.user.title"></b-image>
                  </div>
                </template>

                <div class="media-content">
                  <p class="is-size-6 mb-0">{{ image.title }}</p>
                  <p class="is-size-7 mb-0">by {{ image.user.title }}</p>
                </div>
              </div>

              <p class="has-text-right mb-0">
                <template v-if="!image.public">
                  <b-icon icon="eye-slash" pack="fas"></b-icon>
                </template>

                <template v-if="image.nsfw">
                  <b-icon icon="minus-circle" pack="fas"></b-icon>
                </template>
              </p>
            </div>
          </div>
        </router-link>
      </template>

      <template v-else>
        <b-loading active :is-full-page="false"></b-loading>
      </template>
    </div>
  `
};
