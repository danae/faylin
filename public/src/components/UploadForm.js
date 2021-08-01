// Upload form component
export default {
  // The data for the component
  data: function() {
    return {
      image: null,
      message: 'Upload or drag an image here or paste one from your clipboard',
    }
  },

  // The methods for the component
  methods: {
    // Upload a file
    upload: async function(file)
    {
      // Set the form image
      this.image = URL.createObjectURL(file);

      // Set the form message
      const originalMessage = this.message;
      this.message = this.$iconText('spinner fa-spin', 'Uploading image...');

      // Upload the file from the form
      try
      {
        // Send a capabilities request
        const capabilities = await this.$client.getCapabilities();

        // Check if the file is a correct type
        if (!(file.type in capabilities.supportedContentTypes))
          throw new Error(`Unsupported file type, supported types are ${Object.values(capabilities.supportedContentTypes).join(', ')}`);

        // Check if the file is not too big
        if (file.size > capabilities.supportedSize)
          throw new Error(`Unsupported file size, maximal supported size is ${this.$formatBytes(capabilities.supportedSize)}`);

        // Send an image upload request
        const image = await this.$client.uploadImage(file);

        // Emit the success event
        this.$emit('upload-success', image);
      }
      catch (error)
      {
        // Emit the error event
        this.$emit('upload-error', error);
      }
      finally
      {
        // Reset the file input
        this.$refs.fileInput.value = '';

        // Revoke the image
        URL.revokeObjectURL(this.image);

        // Reset the form image and message
        this.image = null;
        this.message = originalMessage;
      }
    },

    // Upload a file from a DataTransfer object
    uploadDataTransfer: async function(dataTransfer)
    {
      // Check if the data tranfser items are available
      if (dataTransfer.items)
      {
        // Upload the first item as a file if there is one
        if (dataTransfer.items.length > 0 && dataTransfer.items[0].kind === 'file')
          await this.upload(dataTransfer.items[0].getAsFile());
      }
      else
      {
        // Upload the first file if there is one
        if (dataTransfer.files.length > 0)
          await this.upload(dataTransfer.files[0]);
      }
    },

    // Event handler when a file is selected using the file input
    onFileInput: async function(event)
    {
      // Upload the first file if there is one
      if (event.target.files.length > 0)
        await this.upload(event.target.files[0]);
    },

    // Event handler when a file is dropped
    onFileDrop: async function(event)
    {
      console.log("Dropped", event);
      // Upload the data transfer from the event
      await this.uploadDataTransfer(event.dataTransfer);
    },

    // Event handler when a file is pasted
    onFilePaste: async function(event)
    {
      // Upload the clipboard data from the event
      await this.uploadDataTransfer(event.clipboardData);
    },
  },

  // Hook when the component is created
  created: function()
  {
    // Add the file paste event handler
    window.addEventListener('paste', this.onFilePaste);
  },

  // Hook when the component is destroyed
  destroyed: function()
  {
    // Remove the file paste event handler
    window.removeEventListener('paste', this.onFilePaste);
  },

  // The template for the component
  template: `
    <form id="upload-form">
      <div class="box is-primary has-text-centered" @dragenter.prevent @dragover.prevent @drop="onFileDrop">
        <div class="mb-3">
          <template v-if="image">
            <img class="is-uploading" :src="image">
          </template>

          <template v-else>
            <a class="is-borderless" @click.prevent="$refs.fileInput.click()">
              <b-icon pack="fas" icon="upload fa-3x" size="is-large"></b-icon>
            </a>
          </template>
        </div>

        <p class="has-text-primary" v-html="message"></p>
      </div>

      <input type="file" name="file" id="file" ref="fileInput" class="is-hidden" @change.prevent="onFileInput">
    </form>
  `
};
