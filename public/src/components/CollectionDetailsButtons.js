import Collection from '../api/Collection.js';


// Collection details buttons component
export default {
  // The properties for the component
  props: {
    // The collection to reference in the component
    collection: {type: Collection},

    // If the collection is owned by the client user
    owner: {type: Boolean, default: false},

    // If the collection is currently being edited
    editing: {type: Boolean, default: false},
  },

  // The template for the component
  template: `
    <div class="collection-details-buttons">
      <template v-if="collection">
        <div class="level is-mobile">
          <div class="level-left">
            <div class="level-item mx-0">
              <b-tooltip label="Share">
                <b-button type="is-text" icon-left="share-alt" icon-pack="fas" @click="$emit('share')"></b-button>
              </b-tooltip>
            </div>
          </div>

          <template v-if="owner">
            <div class="level-right">
              <div class="level-item mx-0">
                <template v-if="editing">
                  <b-tooltip label="Cancel editing" key="cancel">
                    <b-button type="is-text" icon-left="times" icon-pack="fas" @click="$emit('edit')"></b-button>
                  </b-tooltip>
                </template>

                <template v-else>
                  <b-tooltip label="Edit collection" key="edit">
                    <b-button type="is-text" icon-left="pencil-alt" icon-pack="fas" @click="$emit('edit')"></b-button>
                  </b-tooltip>
                </template>
              </div>

              <div class="level-item mx-0">
                <b-tooltip label="Delete collection" type="is-danger">
                  <b-button type="is-text" icon-left="trash-alt" icon-pack="fas" @click="$emit('delete')"></b-button>
                </b-tooltip>
              </div>
            </div>
          </template>
        </div>
      </template>
    </div>
  `
};
