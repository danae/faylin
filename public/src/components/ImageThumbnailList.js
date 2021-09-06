// Image thumbnail list component
export default {
  // The properties for the component
  props: {
    images: {type: Array},
    fixed: {type: Boolean, default: false},
  },

  // The template for the component
  template: `
    <div class="image-thumbnail-list">
      <template v-if="images">
        <div :class="{'columns': true, 'is-fixed': fixed}">
          <div class="column" v-for="image in images" :key="image.id">
            <image-thumbnail :image="image"></image-thumbnail>
          </div>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
