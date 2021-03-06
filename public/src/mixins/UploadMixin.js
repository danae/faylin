// Upload mixin component
export default {
  // The properties for the mixin
  props: {
    // If specified, then the specified image is replaced, otherwise a new image is created
    replace: {type: String, default: null},

    // The drop behaviour of the mixin
    drop: {type: Boolean, default: false},
    dropTarget: {type: Object, default: null},

    // The paste behaviour of the mixin
    paste: {type: Boolean, default: false},
    pasteTarget: {type: [Object, String], default: null},
  },

  // The data for the mixin
  data: function() {
    return {
      // The file input element for the mixins
      $file: null,
    };
  },

  // Hook when the mixin is created
  created: function() {
    // Create the file input element
    this.$file = document.createElement('input');
    this.$file.type = 'file';
    this.$file.className = 'is-hidden';
    this.$file.onchange = this.onFileInput.bind(this);

    this.$root.$el.append(this.$file);

    // Register event handlers for the drop behaviour
    if (this.drop && this.dropTarget !== null)
    {
      this.dropTarget.$on('dragenter', event => { event.preventDefault(); });
      this.dropTarget.$on('dragover', event => { event.preventDefault(); });
      this.dropTarget.$on('drop', this.onFileDrop.bind(this));
    }

    // Register event handlers for the paste behaviour
    if (this.paste && this.pasteTarget !== null)
    {
      if (this.pasteTarget === 'window')
        window.addEventListener('paste', this.onFilePaste.bind(this));
      else
        this.pasteTarget.$on('paste', this.onFilePaste.bind(this));
    }
  },

  // Hook when the mixin is destroyed
  destroyed: function() {
    // Remove the file input element
    this.$file.remove();

    // Unregister event handlers for the drop behaviour
    if (this.drop && this.dropTarget !== null)
    {
      this.dropTarget.$off('dragenter');
      this.dropTarget.$off('dragover');
      this.dropTarget.$off('drop');
    }

    // Unregister event handlers for the paste behaviour
    if (this.paste && this.pasteTarget !== null)
    {
      if (this.pasteTarget === 'window')
        window.removeEventListener('paste', this.onFilePaste.bind(this));
      else
        this.pasteTarget.$off('paste');
    }
  },

  // The methods for the mixin
  methods: {
    // Click the file input
    inputFile: function() {
      // Simulate a click on the file input
      this.$file.click();
    },

    // Event handler when a file is selected using the file input
    onFileInput: async function(event) {
      // Upload the first file from the event target
      await this.$uploadFileInput(event.target);
    },

    // Event handler when a file is dropped
    onFileDrop: async function(event) {
      // Upload the data transfer from the event
      await this.$uploadDataTransfer(event.dataTransfer);
    },

    // Event handler when a file is pasted
    onFilePaste: async function(event) {
      // Upload the clipboard data from the event
      await this.$uploadDataTransfer(event.clipboardData);
    },

    // Format an anmount of bytes to a human-readable representation
    $formatBytes: function(bytes, decimals = 2) {
      if (bytes === 0)
        return '0 bytes';

      const k = 1024;
      const dm = decimals < 0 ? 0 : decimals;
      const sizes = ['bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];

      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    },

    // Base function for file upload requests
    $uploadFileRequest: async function(file, request) {
      // Upload the file from the form
      try
      {
        // Enit an upload start event
        this.$emit('upload-start', file);

        // Check if the file is the correct type and size
        const capabilities = await this.$root.client.getCapabilities();
        if (!capabilities.supportedContentTypes.includes(file.type))
          throw new Error(`Unsupported file type, supported types are ${Object.values(capabilities.supportedContentTypes).join(', ')}`);
        if (file.size > capabilities.supportedSize)
          throw new Error(`Unsupported file size, maximal supported size is ${this.$formatBytes(capabilities.supportedSize)}`);

        // Send an image upload request and return the response
        const response = await request(file);

        // Emit upload end and success events
        this.$emit('upload-end', file);
        this.$emit('upload-success', response);

        // Return the response
        return response;
      }
      catch (error)
      {
        // Emit upload end and error events
        this.$emit('upload-end', file);
        this.$emit('upload-error', error);
      }
    },

    // Base function for data transfer upload requests
    $uploadDataTransferRequest: async function(dataTransfer, request) {
      // Check if the data tranfser items are available
      if (dataTransfer.items)
      {
        // Upload the first item as a file if there is one
        if (dataTransfer.items.length > 0 && dataTransfer.items[0].kind === 'file')
          return await this.$uploadFileRequest(dataTransfer.items[0].getAsFile(), request);
      }
      else if (dataTransfer.files)
      {
        // Upload the first file if there is one
        if (dataTransfer.files.length > 0)
          return await this.$uploadFileRequest(dataTransfer.files[0], request);
      }
      else
      {
        // No valid data transfer
        throw new TypeError('dataTransfer must be a valid DataTransfer instance and contain either an items or files array');
      }
    },

    // Upload a file
    $uploadFile: async function(file) {
      // Send an upload request and return the response
      if (this.replace === null)
        return await this.$uploadFileRequest(file, file => this.$root.client.uploadImage(file));
      else
        return await this.$uploadFileRequest(file, file => this.$root.client.replaceImage(this.replace, file));
    },

    // Upload a file from a file input element
    $uploadFileInput: async function(fileInput) {
      // Check if the file input contains any files
      if (fileInput.files && fileInput.files.length > 0)
        // Send an upload request and return the response
        return await this.$uploadFile(fileInput.files[0]);
      else
        // No files were present
        return undefined;
    },

    // Upload a data transfer
    $uploadDataTransfer: async function(dataTransfer) {
      // Send an upload request and return the response
      if (this.replace === null)
        return await this.$uploadDataTransferRequest(dataTransfer, file => this.$root.client.uploadImage(file));
      else
        return await this.$uploadDataTransferRequest(dataTransfer, file => this.$root.client.replaceImage(this.replace, file));
    },
  },
};
