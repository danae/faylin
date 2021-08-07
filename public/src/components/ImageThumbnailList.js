// Image thumbnail list component
export default {
  // The properties for the component
  props: {
    images: {type: Array},
    displayName: {type: Boolean, default: true},
    displayUserName: {type: Boolean, default: true},
  },

  // The template for the component
  template: `
    <div class="image-thumbnail-list">
      <template v-if="images">
        <div class="columns">
          <div class="column" v-for="image in images" :key="image.id">
            <image-thumbnail :image="image" :display-name="displayName" :display-user-name="displayUserName"></image-thumbnail>
          </div>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
