import PasswordConfirmMixin from '../mixins/PasswordConfirmMixin.js';


// Settings sessions panel component
export default {
  // The data for the component
  data: function() {
    return {
      // The active sessions for the authorized user
      sessions: null,
    };
  },

  // Hook when the component is created
  created: async function() {
    // Get the active sessions for the authorized user
    this.sessions = await this.$root.client.getAuthorizedUserSessions();
  },

  // The methods for the component
  methods: {
    // Log out a session
    logoutSession: async function(sessionId) {
      // Clear the sessions
      this.sessions = null;

      // Delete the session
      await this.$root.client.deleteAuthorizedUserSession(sessionId);

      // Refetch the sessions
      this.sessions = await this.$root.client.getAuthorizedUserSessions();
    },

    // Format a date
    formatDate: function(date) {
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      const now = new Date();
      const diff = Math.floor((now - date) / 1000);

      if (diff < 60)
        return `Just now`;
      else if (diff < 3600)
        return `${Math.floor(diff / 60)} minutes ago`;
      else if (diff < 86400)
        return `${Math.floor(diff / 3600)} hours ago`;
      else
        return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
    },
  },

  // The template for the component
  template: `
    <div class="settings-sessions-panel mb-5">
      <p class="menu-label">
        <span class="icon-text">
          <b-icon icon="users" pack="fas"></b-icon>
          <span>Manage sessions</span>
        </span>
      </p>

      <template v-if="sessions">
        <b-table :data="sessions" :mobile-cards="false" class="box is-panel is-size-7 p-0">
          <b-table-column field="date" label="Login date" width="20%" v-slot="props">
            {{ formatDate(props.row.createdAt) }}
          </b-table-column>

          <b-table-column field="browser" label="Browser" width="40%" v-slot="props">
            <template v-if="props.row.info">
              {{ props.row.info.browser }}

              <template v-if="props.row.info.os">
                on {{ props.row.info.os }}
              </template>

              <template v-if="props.row.info.device">
                ({{ props.row.info.device }})
              </template>
            </template>

            <template v-else>
              <span class="has-text-grey-light">{{ props.row.userAgent }}</span>
            </template>
          </b-table-column>

          <b-table-column field="ip" label="IP address" width="20%" v-slot="props">
            {{ props.row.userAddress }}
          </b-table-column>

          <b-table-column field="actions" label="Actions" width="20%" numeric v-slot="props">
            <template v-if="props.row.current">
              <span class="has-text-grey-light">Current session</span>
            </template>

            <template v-else>
              <a @click="logoutSession(props.row.id)">Log out</a>
            </template>
          </b-table-column>
        </b-table>
      </template>

      <template v-else>
        <b-loading></b-loading>
      </template>
    </div>
  `
};
