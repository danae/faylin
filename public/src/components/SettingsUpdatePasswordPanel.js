import PasswordConfirmMixin from '../mixins/PasswordConfirmMixin.js';


// Settings update password panel component
export default {
  // The mixins for the component
  mixins: [PasswordConfirmMixin],

  // Hook when the component is created
  created: function() {
    // Register event handler for password confirmation
    this.$on('password-confirmed', this.onPasswordConfirmed.bind(this));
  },

  // Hook when the component is destroyed
  destroyed: function() {
    // Unregister event handler for password confirmation
    this.$off('password-confirmed');
  },

  // The methods for the component
  methods: {
    // Event handler when the form is submitted
    onSubmit: async function(event) {
      // Get the form values
      const password = event.target.elements.password.value;
      const passwordRepeat = event.target.elements.passwordRepeat.value;

      // Check if the password is not empty
      if (password === '' && passwordRepeat === '')
        // Display an error message
        this.$displayMessage('The password field is empty', 'is-danger');
      else if (password !== passwordRepeat)
        // Display an error message
        this.$displayMessage('The passwords did not match', 'is-danger');
      else
        // Ask for password confirmation
        this.confirmPassword(password);

      // Reset the form values
      event.target.elements.password.value = '';
      event.target.elements.passwordRepeat.value = '';
    },

    // Event handler when the password is confirmed
    onPasswordConfirmed: async function(password, currentPassword) {
      // Send an update email request
      await this.$root.client.updateAuthorizedUserPassword(password, currentPassword);

      // Display a success message
      this.$displayMessage('Password updated successfully');
    },
  },

  // The template for the component
  template: `
    <div class="settings-update-password-panel mb-5">
      <form ref="form" @submit.prevent="onSubmit">
        <p class="menu-label">
          <span class="icon-text">
            <b-icon icon="key" pack="fas"></b-icon>
            <span>Update password</span>
          </span>
        </p>

        <div class="box is-panel">
          <b-field label="New password" custom-class="is-small">
            <b-input type="password" name="password"></b-input>
          </b-field>

          <b-field label="Repeat new password" custom-class="is-small">
            <b-input type="password" name="passwordRepeat"></b-input>
          </b-field>

          <b-button type="is-primary" icon-left="save" icon-pack="fas" @click="$refs.form.requestSubmit()">
            Save password
          </b-button>
        </div>
      </form>
    </div>
  `
};
