import User from '../api/User.js';


// User thumbnail component
export default {
  // The properties for the component
  props: {
    user: {type: User},
  },

  // The data for the component
  data: function() {
    return {
      userImages: null,
    }
  },

  // Hook when the component is created
  created: async function() {
  // Get the images for the user
    if (this.user !== null)
      this.userImages = await this.$root.client.getUserImages(this.user.id, {perPage: 4});
  },

  // The template for the component
  template: `
    <div class="user-thumbnail content">
      <template v-if="user">
        <div class="box is-panel">
          <router-link :to="{name: 'user', params: {userId: user.id}}">
            <div class="media is-align-items-center mb-4">
              <template v-if="user.avatarUrl">
                <div class="media-left mr-2">
                  <b-image class="avatar is-48x48" :src="user.avatarUrl" :alt="user.title"></b-image>
                </div>
              </template>

              <div class="media-content">
                <p class="is-size-6 has-text-weight-bold mb-0">
                  {{ user.title }}
                </p>
                <p class="is-size-7 mb-0">
                  @{{ user.name }}
                </p>
              </div>
            </div>
          </router-link>

          <template v-if="user.description">
            <p class="is-size-7">{{ user.description }}</p>
          </template>

          <template v-if="userImages">
            <image-thumbnail-list :images="userImages"></image-thumbnail-list>
          </template>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
