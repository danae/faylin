// Settings update profile form component
export default {
  // The methods for the component
  methods: {
    // Event handler when the form is submitted
    onSubmit: async function(event) {
      // Send a patch request
      await this.$root.client.patchAuthorizedUser({
        name: this.$root.clientUser.name,
        title: this.$root.clientUser.title,
        description: this.$root.clientUser.description,
        public: this.$root.clientUser.public,
      });

      // Display a success message
      this.$displayMessage('Profile updated successfully');
    },
  },

  // The template for the component
  template: `
    <div class="settings-update-email-panel mb-5">
      <form ref="form" @submit.prevent="onSubmit">
        <p class="menu-label">
          <span class="icon-text">
            <b-icon icon="user-circle" pack="fas"></b-icon>
            <span>Update profile</span>
          </span>
        </p>

        <div class="box is-panel">
          <b-field label="Name" message="This is the name as it appears in your profile URL." custom-class="is-small">
            <b-input v-model="$root.clientUser.name" type="text" maxlength="32" :has-counter="false" name="name"></b-input>
          </b-field>

          <b-field label="Display name" message="This is the name that is displayed at your profile, images and other resources." custom-class="is-small">
            <b-input v-model="$root.clientUser.title" type="text" maxlength="64" :has-counter="false" name="title"></b-input>
          </b-field>

          <b-field label="Description" custom-class="is-small">
            <b-input v-model="$root.clientUser.description" type="textarea" maxlength="256" :has-counter="false" name="description"></b-input>
          </b-field>

          <b-field label="Visibility settings" custom-class="is-small">
            <b-switch v-model="$root.clientUser.public">Listed publicly</b-switch>
          </b-field>

          <b-button type="is-primary" icon-left="save" icon-pack="fas" @click="$refs.form.requestSubmit()">
            Save profile
          </b-button>
        </div>
      </form>
    </div>
  `
};
