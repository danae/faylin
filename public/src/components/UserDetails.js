import User from '../api/User.js';


// User details component
export default {
  // The properties for the component
  props: {
    user: {type: User},
  },

  // The data for the component
  data: function()
  {
    return {
      userImages: null,
    }
  },

  // Hook when the component is updated
  updated: async function()
  {
    try
    {
      // Get the images for the user
      if (this.user !== null)
        this.userImages = await this.user.getImages();
    }
    catch (error)
    {
      // Display the error message
      this.$displayErrorMessage(error.message);
    }
  },

  // The template for the component
  template: `
    <div class="user-details">
      <template v-if="user">
        <section class="hero is-primary mb-6">
          <div class="hero-body">
            <p class="title mb-0">{{ user.name }}</p>
          </div>
        </section>

        <image-thumbnail-list :images="userImages" :displayUser="false"></image-thumbnail-list>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};