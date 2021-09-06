// Collection thumbnail list component
export default {
  // The properties for the component
  props: {
    collections: {type: Array},
  },

  // The template for the component
  template: `
    <div class="collection-thumbnail-list">
      <template v-if="collections">
        <div class="columns">
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
