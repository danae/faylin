// Settings route component
export default {
  // The computed data for the route
  computed: {
    // The settings page
    page: function() {
      return this.$route.params.page ?? 'user/account'
    },
  },

  // Hook when the component is created
  created: async function() {
    // Set the document title
    document.title = `Settings – fayl.in`;
  },

  // The template for the route
  template: `
    <div class="settings-page">
      <hr class="bar">

      <div class="container">
        <section class="section">
          <div class="columns">
            <div class="column is-3">
              <b-menu>
                <b-menu-list label="User settings">
                  <b-menu-item label="Account" tag="router-link" to="/settings/user/account" :active="page === 'user/account'"></b-menu-item>
                  <b-menu-item label="Profile" tag="router-link" to="/settings/user/profile" :active="page === 'user/profile'"></b-menu-item>
                  <b-menu-item label="Sessions" tag="router-link" to="/settings/user/sessions" :active="page === 'user/sessions'"></b-menu-item>
                </b-menu-list>

                <b-menu-list label="Actions">
                  <b-menu-item label="Log out" tag="router-link" to="/logout">Log out</b-menu-item>
                </b-menu-list>
              </b-menu>
            </div>

            <div class="column is-9 content">
              <template v-if="page == 'user/account'">
                <settings-user-account></settings-user-account>
              </template>

              <template v-if="page == 'user/profile'">
                <settings-user-profile></settings-user-profile>
              </template>

              <template v-if="page == 'user/sessions'">
                <settings-user-sessions></settings-user-sessions>
              </template>
            </div>
          </div>
        </section>
      </div>
    </div>
  `
};
