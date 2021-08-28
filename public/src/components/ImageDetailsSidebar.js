import Image from '../api/Image.js';


// Image details sidebar component
export default {
  // The properties for the component
  props: {
    // The image to reference in the component
    image: {type: Image},

      // Indicate if the client user is the owner of the image
    owner: {type: Boolean, default: false},
  },

  // The data for the component
  data: function() {
    return {
      // If the image is currently being edited
      editing: false,
    }
  },

  // The methods for the component
  methods: {
    // Share the image
    shareImage: async function() {
      // Share the image
      await navigator.share({url: this.image.downloadUrl, title: this.image.name});
    },

    // Copy the image link to the clipboard
    copyImageLink: async function(template = null) {
      // Get the default template
      template ??= image => image.downloadUrl;

      // Check for the clipboard permission
      const permission = await navigator.permissions.query({name: "clipboard-write"});
      if (permission.state == "granted" || permission.state == "prompt")
      {
        // Write the link to the clipboard
        await navigator.clipboard.writeText(template(this.image));

        // Display a success message
        this.$displayMessage('Image link copied succesfully');
      }
      else
      {
        // Display an error message
        this.$displayMessage('No permission to use the clipboard', 'is-danger', 5000);
      }
    },

    // Copy the image as Markdown to the clipboard
    copyImageMarkdown: async function() {
      await this.copyImageLink(image => `![${image.name}](${image.downloadUrl})`);
    },

    // Copy the image as BBCode to the clipboard
    copyImageBBCode: async function() {
      await this.copyImageLink(image => `[img]${image.downloadUrl}[/img]`);
    },

    // Copy the image as HTML to the clipboard
    copyImageHTML: async function() {
      await this.copyImageLink(image => `<img src="${image.downloadUrl}" alt="${image.name}">`);
    },

    // Delete the image
    deleteImage: async function() {
      // Show the confirmation dialog
      this.$buefy.dialog.confirm({
        type: 'is-danger',
        hasIcon: true,
        icon: 'trash-alt',
        iconPack: 'fas',
        trapFocus: true,
        message: `Are you sure you want to delete <b>${this.image.name}</b>? All associated data and links to the image will stop working forever, which is a long time!`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
        onConfirm: await this.deleteImageConfirmed.bind(this),
      });
    },

    // Delete the image after confirmation
    deleteImageConfirmed: async function() {
      // Send a delete request
      await this.$root.client.deleteImage(this.image.id);

      // Display a success message
      this.$displayMessage('Image deleted succesfully');

      // Redirect to the previous page
      this.$router.back();
    },

    // Toggle the editing state
    toggleEditing: function() {
      this.editing = !this.editing;
    },
  },

  // The template for the component
  template: `
    <div class="image-details-sidebar">
      <template v-if="image">
        <image-details-sidebar-buttons :image="image" :owner="owner" :editing="editing" class="mb-3" @share="shareImage()" @copy-link="copyImageLink()" @copy-markdown="copyImageMarkdown()" @copy-bbcode="copyImageBBCode()" @copy-html="copyImageHTML()" @edit="toggleEditing()" @delete="deleteImage"></image-details-sidebar-buttons>

        <template v-if="editing">
          <image-details-edit-panel :image="image" :replace="image.id" @close="toggleEditing()"></image-details-edit-panel>
        </template>

        <template v-else>
          <h2 class="image-details-name mb-0">{{ image.name }}</h2>
          <p class="image-details-user-name">by <router-link :to="{name: 'user', params: {userId: image.user.id }}">{{ image.user.name }}</router-link></p>

          <template v-if="image.description">
            <p>{{ image.description }}</p>
          </template>
        </template>
      </template>

      <template v-else>
        <b-loading active :is-full-page="false"></b-loading>
      </template>
    </div>
  `
};
