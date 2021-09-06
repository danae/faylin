// Collection thumbnail list component
export default {
  // The properties for the component
  props: {
    collections: {type: Array},
    responsiveSmall: {type: Boolean, default: false},
    responsiveLarge: {type: Boolean, default: false},
  },

  // The template for the component
  template: `
    <div class="thumbnail-list">
      <template v-if="collections">
        <div :class="{'columns': true, 'is-responsive-small': responsiveSmall, 'is-responsive-large': responsiveLarge}">
          <div class="column" v-for="collection in collections" :key="collection.id">
            <collection-thumbnail :collection="collection"></collection-thumbnail>
          </div>
        </div>
      </template>

      <template v-else>
        <b-loading active></b-loading>
      </template>
    </div>
  `
};
