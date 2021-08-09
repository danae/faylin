// Login route component
export default {
  // The data for the route
  data: function() {
    return {
      username: '',
      password: ''
    }
  },

  // Hook when the component is created
  created: function() {
    // Set the document title
    document.title = `Log in – fayl.in`;
  },

  // The methods for the route
  methods: {
    // Submit the form
    onSubmit: function() {
      // Send a login request
      this.$login(this.username, this.password)
        .then(this.onLoginSuccess.bind(this), this.onLoginError.bind(this));
    },

    // Cancel the form
    onCancel: function() {
      // Redirect to the previous page
      this.$router.back();
    },

    // Event handler when the login has succeeded
    onLoginSuccess: function() {
      // Display a success message
      this.$displayMessage('Logged in succesfully');

      // Redirect to the page specified by the query, or the home page otherwise
      let query = new URLSearchParams(window.location.search);
      if (query.has('redirect'))
        this.$router.push(query.get('redirect'));
      else
        this.$router.push('/');
    },

    // Event handler when the login was unsuccessful
    onLoginError: function(error) {
      // Display the error
      this.$displayError(error);
    },
  },

  // The template for the route
  template: `
    <div class="login-page">
      <section class="section">
        <div class="columns is-centered">
          <div class="column is-half">
            <div class="box is-primary">
              <p>Log in to fayl.in to be able to upload and organize your images.</p>

              <form @submit.prevent="onSubmit">
                <b-field label="Email address" label-position="on-border">
                  <b-input v-model="username" type="email" name="username" id="username" icon-pack="fas" icon="envelope" autofocus></b-input>
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
          </div>
        </div>
      </section>
    </div>
  `
};
