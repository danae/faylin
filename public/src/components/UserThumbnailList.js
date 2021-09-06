// User thumbnail list component
export default {
  // The properties for the component
  props: {
    users: {type: Array},
  },

  // The template for the component
  template: `
    <div class="user-thumbnail-list">
      <template v-if="users">
        <section class="section">
          <div class="columns is-multiline">
            <div class="column is-half" v-for="user in users" :key="user.id">
              <user-thumbnail :user="user"></user-thumbnail>
            </div>
          </div>
        </section>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
