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
        <user-thumbnail v-for="user in users" :key="user.id" :user="user"></user-thumbnail>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
