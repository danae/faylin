// Login form component
export default {
  // The data for the component
  data: function() {
    return {
      username: '',
      password: ''
    }
  },

  // The methods for the component
  methods: {
    // Submit the form
    onSubmit: async function() {
      // Send a login request
      return await this.$login(this.username, this.password);
    },

    // Cancel the form
    onCancel: function() {
      // Redirect to the previous page
      this.$router.back();
    },
  },

  // The template for the component
  template: `
    <div class="login-form box is-primary">
      <p>Log in to fayl.in to be able to upload and organize your images.</p>

      <form @submit.prevent="onSubmit">
        <b-field label="Email address" label-position="on-border">
          <b-input v-model="username" type="text" name="username" id="username" icon-pack="fas" icon="envelope" autofocus></b-input>
        </b-field>

        <b-field label="Password" label-position="on-border">
          <b-input v-model="password" type="password" name="password" id="password" icon-pack="fas" icon="key"></b-input>
        </b-field>

        <b-field grouped>
          <div class="control">
            <b-button type="is-primary" icon-pack="fas" icon-left="check" native-type="submit">Sign in</b-button>
          </div>

          <div class="control">
            <b-button type="is-light" @click="onCancel">Cancel</b-button>
          </div>
        </b-field>
      </form>
    </div>
  `
};
