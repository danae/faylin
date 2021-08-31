import Collection from '../api/Collection.js';


// Collection thumbnail component
export default {
  // The properties for the component
  props: {
    collection: {type: Collection},
    displayName: {type: Boolean, default: true},
    displayUserName: {type: Boolean, default: true},
  },

  // The template for the component
  template: `
    <div class="collection-thumbnail content">
      <template v-if="collection">
        <router-link :to="{name: 'collection', params: {collectionId: collection.id}}">
          <template v-if="collection.images.length > 0">
            <b-image class="collection-thumbnail-image" :src="collection.images[0].thumbnailUrl" :alt="collection.images[0].name"></b-image>
          </template>

          <template v-else>
            Collection
          </template>
        </router-link>

        <template v-if="displayName">
          <h4 class="collection-thumbnail-name mb-0">{{ collection.name }}</h4>
        </template>

        <template v-if="displayUserName">
          <p class="collection-thumbnail-user-name mb-0">by <router-link :to="{name: 'user', params: {userId: collection.user.id}}">{{ collection.user.name }}</router-link></p>
        </template>
      </template>

      <template v-else>
        <b-loading active :is-full-page="false"></b-loading>
      </template>
    </div>
  `
};
