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
      this.userImages = await this.$root.client.getUserImages(this.user.id, {perPage: 3});
  },

  // The template for the component
  template: `
    <div class="user-thumbnail content">
      <template v-if="user">
        <div class="box is-panel my-2">
          <div class="columns">
            <div class="column is-4">
              <div class="media is-align-items-center mb-4">
                <template v-if="user.avatarUrl">
                  <div class="media-left mr-2">
                    <router-link :to="{name: 'user', params: {userId: user.id}}">
                      <b-image class="avatar is-48x48" :src="user.avatarUrl" :alt="user.title"></b-image>
                    </router-link>
                  </div>
                </template>

                <div class="media-content">
                  <p class="is-size-6 mb-0">
                    <router-link :to="{name: 'user', params: {userId: user.id}}">{{ user.title }}</router-link>
                  </p>
                  <p class="is-size-7 mb-0">
                    @{{ user.name }}
                  </p>
                </div>
              </div>

              <template v-if="user.description">
                <p class="is-size-7">{{ user.description }}</p>
              </template>
            </div>

            <div class="column is-8">
              <template v-if="userImages">
                <image-thumbnail-list :images="userImages" :fixed="true"></image-thumbnail-list>
              </template>
            </div>
          </div>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
