import User from '../api/User.js';


// User details component
export default {
  // The properties for the component
  props: {
    // The user to show the details of
    user: {type: User},
  },

  // The data for the component
  data: function() {
    return {
      // The images and collections for the user
      userImages: null,
      userCollections: null,
    }
  },

  // The computed data for the component
  computed: {
    // The page to display
    page: function() {
      return this.$route.params.page ?? 'images'
    },
  },

  // The watchers for the component
  watch: {
    user: async function(newUser) {
      // Get the images for the user
      if (newUser !== null)
      {
        this.userImages = await this.$root.client.getUserImages(newUser.id);
        this.userCollections = await this.$root.client.getUserCollections(newUser.id);
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
          <div class="tabs is-centered mb-0">
            <ul>
              <li :class="{'is-active': page === 'images'}">
                <router-link :to="{name: 'user', params: {userId: user.id, page: 'images'}}">
                  Images
                  <template v-if="userImages && userImages.length > 0">
                    <b-tag type="is-light" class="ml-2">{{ userImages.length }}</b-tag>
                  </template>
                </router-link>
              </li>

              <li :class="{'is-active': page === 'collections'}">
                <router-link :to="{name: 'user', params: {userId: user.id, page: 'collections'}}">
                  Collections
                  <template v-if="userCollections && userCollections.length > 0">
                    <b-tag type="is-light" class="ml-2">{{ userCollections.length }}</b-tag>
                  </template>
                </router-link>
              </li>
            </ul>
          </div>

          <template v-if="page === 'images'">
            <section class="section">
              <image-thumbnail-list :images="userImages" responsiveLarge></image-thumbnail-list>
            </section>
          </template>

          <template v-if="page === 'collections'">
            <section class="section">
              <collection-thumbnail-list :collections="userCollections" responsiveLarge></collection-thumbnail-list>
            </section>
          </template>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
