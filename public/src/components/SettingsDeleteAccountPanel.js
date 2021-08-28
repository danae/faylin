import ClientErrorCaptureMixin from '../mixins/ClientErrorCaptureMixin.js';
import PasswordConfirmMixin from '../mixins/PasswordConfirmMixin.js';


// Settings delete account form component
export default {
  // The mixins for the component
  mixins: [PasswordConfirmMixin, ClientErrorCaptureMixin],

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
    onSubmit: async function() {
      // Show the confirmation dialog
      this.$buefy.dialog.confirm({
        type: 'is-danger',
        hasIcon: true,
        icon: 'trash-alt',
        iconPack: 'fas',
        trapFocus: true,
        message: `Are you really sure you want to delete your account? If you delete your account, all of your associated data, including shared images, will be removed forever, which is a long time! Please note that deleted accounts and their shared images can't be recovered!`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
        onConfirm: await this.onSubmitConfirmed.bind(this),
      });
    },

    // Event handler when the form submit is confirmed
    onSubmitConfirmed: async function(currentPassword) {
      // Ask for password confirmation
      this.confirmPassword();
    },

    // Event handler when the password is confirmed
    onPasswordConfirmed: async function(currentPassword) {
      // Send an update email request
      await this.$root.client.deleteAuthorizedUser(currentPassword);

      // Display a success message
      this.$displayMessage('User deleted successfully');

      // Send a logout request
      this.$root.$logout();

      // Redirect to the home page
      this.$router.push({name: 'home'});
    },
  },

  // The template for the component
  template: `
    <div class="settings-update-password-form panel is-danger">
      <form ref="form" @submit.prevent="onSubmit">
        <p class="panel-heading">Delete account</p>

        <div class="panel-block">
          <p>If you delete your account, all of your associated data, including shared images, will be removed forever, which is a long time! Please note that deleted accounts and their shared images can't be recovered!</p>
        </div>

        <a class="panel-block has-text-danger" @click="$refs.form.requestSubmit()">
          <b-icon icon="trash-alt" pack="fas" type="is-danger" class="panel-icon"></b-icon> Delete account
        </a>
      </form>
    </div>
  `
};
