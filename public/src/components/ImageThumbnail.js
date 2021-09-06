import Image from '../api/Image.js';


// Image thumbnail component
export default {
  // The properties for the component
  props: {
    image: {type: Image},
  },

  // The template for the component
  template: `
    <div class="image-thumbnail content">
      <template v-if="image">
        <router-link :to="{name: 'image', params: {imageId: image.id}}">
          <div class="is-relative">
            <b-image class="image-thumbnail-image" :src="image.thumbnailUrl" :alt="image.title" :class="{'is-nsfw': image.nsfw}"></b-image>

            <div class="image-thumbnail-cover">
              <div class="media is-align-items-center mb-4">
                <template v-if="image.user.avatarUrl">
                  <div class="media-left mr-2">
                    <b-image class="avatar is-32x32" :src="image.user.avatarUrl" :alt="image.user.title"></b-image>
                  </div>
                </template>

                <div class="media-content">
                  <p class="is-size-6 mb-0">
                    {{ image.title }}
                  </p>
                  <p class="is-size-7 mb-0">
                    <router-link :to="{name: 'user', params: {userId: image.user.id}}" class="has-text-white">by {{ image.user.title }}</router-link>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </router-link>

        <!--

        -->
      </template>

      <template v-else>
        <b-loading active :is-full-page="false"></b-loading>
      </template>
    </div>
  `
};
