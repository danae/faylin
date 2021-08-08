import PasswordConfirmMixin from '../mixins/PasswordConfirmMixin.js';


// Settings update email form component
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
      await this.$root.client.updateMeEmail(email, currentPassword);

      // Display a success message
      this.$displayMessage('Email address updated successfully');
    },
  },

  // The template for the component
  template: `
    <div class="settings-update-email-form panel is-primary">
      <form ref="form" @submit.prevent="onSubmit">
        <p class="panel-heading">Update email address</p>

        <div class="panel-block is-form">
          <b-field label="Email address" label-position="on-border">
            <b-input v-model="clientUserEmail" type="email" name="email" icon-pack="fas" icon="envelope"></b-input>
          </b-field>
        </div>

        <a class="panel-block" @click="$refs.form.requestSubmit()">
          <b-icon icon="save" pack="fas" class="panel-icon"></b-icon> Save email address
        </a>
      </form>
    </div>
  `
};
