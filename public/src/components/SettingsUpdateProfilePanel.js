// Settings update profile form component
export default {
  // The methods for the component
  methods: {
    // Event handler when the form is submitted
    onSubmit: async function(event) {
      // Send a patch request
      await this.$root.client.patchAuthorizedUser({name: this.$root.clientUser.name});

      // Display a success message
      this.$displayMessage('Profile updated successfully');
    },
  },

  // The template for the component
  template: `
    <div class="settings-update-email-form panel is-primary">
      <form ref="form" @submit.prevent="onSubmit">
        <p class="panel-heading">Update profile</p>

        <div class="panel-block is-form">
          <b-field label="Name" custom-class="is-small">
            <b-input v-model="$root.clientUser.name" type="text" name="name"></b-input>
          </b-field>
        </div>

        <a class="panel-block" @click="$refs.form.requestSubmit()">
          <b-icon icon="save" pack="fas" class="panel-icon"></b-icon> Save profile
        </a>
      </form>
    </div>
  `
};
