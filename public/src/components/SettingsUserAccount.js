// User account settings component
export default {
  // The template for the component
  template: `
    <div class="settings-user-account">
      <h3 class="mb-0">Account</h3>
      <p>Change how you log in to your user account.</p>

      <settings-update-email-panel></settings-update-email-panel>
      <settings-update-password-panel></settings-update-password-panel>
      <settings-delete-account-panel></settings-delete-account-panel>
    </div>
  `
};
