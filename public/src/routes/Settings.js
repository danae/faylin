// Settings route component
export default {
  // The data for the route
  data: function() {
    return {
      // The current settings page
      settings: 'user/account',
    }
  },

  // Hook when the component is created
  created: async function() {
    // Set the document title
    document.title = `Settings – fayl.in`;
  },

  // The template for the route
  template: `
    <div class="image-view-page">
      <section class="section">
        <div class="columns">
          <div class="column is-3">
            <b-menu>
              <b-menu-list label="User settings">
                <b-menu-item label="Account" @click="settings = 'user/account'"></b-menu-item>
                <b-menu-item label="Profile" @click="settings = 'user/profile'"></b-menu-item>
              </b-menu-list>
            </b-menu>
          </div>

          <div class="column is-9 content">
            <template v-if="settings == 'user/account'">
              <h3 class="mb-0">Account</h3>
              <p>Change how you log in to your user account.</p>

              <settings-update-email-form></settings-update-email-form>
              <settings-update-password-form></settings-update-password-form>
              <settings-delete-account-form></settings-delete-account-form>
            </template>

            <template v-if="settings == 'user/profile'">
              <h3 class="mb-0">Profile</h3>
              <p>Change the appearance of your user account.</p>

              <settings-update-profile-form></settings-update-profile-form>
            </template>
          </div>
        </div>
      </section>
    </div>
  `
};
