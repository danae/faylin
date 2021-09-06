import User from '../api/User.js';


// User details component
export default {
  // The properties for the component
  props: {
    user: {type: User},
  },

  // The data for the component
  data: function() {
    return {
      userCollections: null,
      userImages: null,
    }
  },

  // The watchers for the component
  watch: {
    user: async function(newUser) {
      // Get the images for the user
      if (newUser !== null)
      {
        this.userCollections = await this.$root.client.getUserCollections(newUser.id);
        this.userImages = await this.$root.client.getUserImages(newUser.id);
      }
      else
      {
        this.userCollections = null;
        this.userImages = null;
      }
    },
  },

  // The template for the component
  template: `
    <div class="user-details">
      <template v-if="user">
        <div class="hero is-light mb-4">
          <div class="hero-body container content">
            <template v-if="user.avatarUrl">
              <b-image class="avatar is-128x128" :src="user.avatarUrl" :alt="user.title"></b-image>
            </template>

            <h1>{{ user.title }}</h1>

            <template v-if="user.description">
              <p>{{ user.description }}</p>
            </template>
          </div>
        </div>

        <div class="container">
          <section class="section">
            <b-tabs position="is-centered" :animated="false">
              <b-tab-item>
                <template #header>
                  <span>
                    Images
                    <template v-if="userImages && userImages.length > 0">
                      <b-tag type="is-light" class="ml-2">{{ userImages.length }}</b-tag>
                    </template>
                  </span>
                </template>

                <template #default>
                  <image-thumbnail-list :images="userImages"></image-thumbnail-list>
                </template>
              </b-tab-item>

              <b-tab-item label="Collections">
                <template #header>
                  <span>
                    Collections
                    <template v-if="userCollections && userCollections.length > 0">
                      <b-tag type="is-light" class="ml-2">{{ userCollections.length }}</b-tag>
                    </template>
                  </span>
                </template>

                <template #default>
                  <collection-thumbnail-list :collections="userCollections"></collection-thumbnail-list>
                </template>
              </b-tab-item>
            </b-tabs>
          </section>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
