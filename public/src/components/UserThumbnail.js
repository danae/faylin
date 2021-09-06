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
        <div class="box is-primary">
          <router-link :to="{name: 'user', params: {userId: user.id}}">
            <h4 class="user-thumbnail-name mb-0">{{ user.title }}</h4>
          </router-link>

          <template v-if="userImages">
            <div class="columns mt-4">
              <div class="column is-one-third" v-for="image in userImages" :key="image.id">
                <image-thumbnail :image="image"></image-thumbnail>
              </div>
            </div>
          </template>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
