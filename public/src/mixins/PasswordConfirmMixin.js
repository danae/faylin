import User from '../api/User.js';


// Password confirm mixin component
export default {
  // The methods for the mixin
  methods: {
    // Ask for password confirmation
    confirmPassword: function(...args) {
      // Show the password prompt
      this.$buefy.dialog.prompt({
        type: 'is-primary',
        hasIcon: true,
        icon: 'key',
        iconPack: 'fas',
        trapFocus: true,
        message: 'Please enter your current password to confirm your action.',
        inputAttrs: {type: 'password'},
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        onConfirm: value => this.$emit('password-confirmed', ...args, value),
      });
    }
  },
};
