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
                <b-menu-item label="Account" :active="settings == 'user/account'" @click="settings = 'user/account'"></b-menu-item>
                <b-menu-item label="Profile" :active="settings == 'user/profile'" @click="settings = 'user/profile'"></b-menu-item>
              </b-menu-list>

              <b-menu-list label="Actions">
                <b-menu-item label="Log out" tag="router-link" :to="{name: 'logout'}">Log out</b-menu-item>
              </b-menu-list>
            </b-menu>
          </div>

          <div class="column is-9 content">
            <template v-if="settings == 'user/account'">
              <settings-user-account></settings-user-account>
            </template>

            <template v-if="settings == 'user/profile'">
              <settings-user-profile></settings-user-profile>
            </template>
          </div>
        </div>
      </section>
    </div>
  `
};
