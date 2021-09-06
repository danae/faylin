import Collection from '../api/Collection.js';


// Collection thumbnail component
export default {
  // The properties for the component
  props: {
    collection: {type: Collection},
  },

  // The template for the component
  template: `
    <div class="thumbnail content">
      <template v-if="collection">
        <router-link :to="{name: 'collection', params: {collectionId: collection.id}}">
          <div class="is-relative">
            <b-image class="thumbnail-image" :src="collection.images[0].thumbnailUrl" :alt="collection.title"></b-image>

            <div class="thumbnail-cover">
              <div class="media is-align-items-center mb-4">
                <template v-if="collection.user.avatarUrl">
                  <div class="media-left mr-2">
                    <b-image class="avatar is-32x32" :src="collection.user.avatarUrl" :alt="collection.user.title"></b-image>
                  </div>
                </template>

                <div class="media-content">
                  <p class="is-size-6 mb-0">{{ collection.title }}</p>
                  <p class="is-size-7 mb-0">by {{ collection.user.title }}</p>
                </div>
              </div>
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
