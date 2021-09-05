import PasswordConfirmMixin from '../mixins/PasswordConfirmMixin.js';


// Settings update email panel component
export default {
  // The mixins for the component
  mixins: [PasswordConfirmMixin],

  // The computed data for the component
  computed: {
    // The email address of the client user
    clientUserEmail: {
      get: function() {
        if (this.$root.clientUser)
          return this.$root.clientUser.email;
        else
          return '';
      },
      set: function(value) {
        if (this.$root.clientUser)
          this.$root.clientUser.email = value;
      },
    },
  },

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
    onSubmit: function(event) {
      // Get the form values
      const email = event.target.elements.email.value;

      // Check if the email is not empty
      if (email === '')
        // Display an error message
        this.$displayMessage('The email field is empty', 'is-danger');
      else
        // Ask for password confirmation
        this.confirmPassword(email);
    },

    // Event handler when the password is confirmed
    onPasswordConfirmed: async function(email, currentPassword) {
      // Send an update email request
      await this.$root.client.updateAuthorizedUserEmail(email, currentPassword);

      // Display a success message
      this.$displayMessage('Email address updated successfully');
    },
  },

  // The template for the component
  template: `
    <div class="settings-update-email-panel mb-5">
      <form ref="form" @submit.prevent="onSubmit">
        <p class="menu-label">
          <span class="icon-text">
            <b-icon icon="envelope" pack="fas"></b-icon>
            <span>Update email address</span>
          </span>
        </p>

        <div class="box is-panel">
          <b-field label="Email address" custom-class="is-small">
            <b-input v-model="clientUserEmail" type="email" name="email"></b-input>
          </b-field>

          <b-button type="is-primary" icon-left="save" icon-pack="fas" @click="$refs.form.requestSubmit()">
            Save email address
          </b-button>
        </div>
      </form>
    </div>
  `
};
