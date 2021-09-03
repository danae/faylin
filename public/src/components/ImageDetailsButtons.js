import Image from '../api/Image.js';


// Image details buttons component
export default {
  // The properties for the component
  props: {
    // The image to reference in the component
    image: {type: Image},

    // The collections the image can be added to
    collections: {type: Array, default: () => []},

    // If the image is owned by the client user
    owner: {type: Boolean, default: false},

    // If the image is currently being edited
    editing: {type: Boolean, default: false},
  },

  // The data for the component
  data: function() {
    return {
      // The title for a new collection to add the image to
      newCollectionTitle: "",
    };
  },

  // The template for the component
  template: `
    <div class="image-details-sidebar-buttons">
      <template v-if="image">
        <div class="level is-mobile">
          <div class="level-left">
            <div class="level-item mx-0">
              <b-tooltip label="Share">
                <b-button type="is-text" icon-left="share-alt" icon-pack="fas" @click="$emit('share')"></b-button>
              </b-tooltip>
            </div>

            <div class="level-item mx-0">
              <b-dropdown>
                <template #trigger>
                  <b-tooltip label="Add to collection">
                    <b-button type="is-text" icon-left="plus" icon-pack="fas"></b-button>
                  </b-tooltip>
                </template>

                <template #default>
                  <template v-if="collections && collections.length > 0">
                    <b-dropdown-item v-for="collection in collections" :key="collection.id" @click="$emit('add', collection)">
                      {{ collection.title }}
                    </b-dropdown-item>

                    <hr class="dropdown-divider">
                  </template>

                  <b-dropdown-item custom>
                    <form @submit.prevent="$emit('add-new', newCollectionName)">
                        <b-field label="New collection" label-for="titletitle" custom-class="is-small">
                          <b-input v-model="newCollectionTitle" expanded type="text" name="title"></b-input>

                          <p class="control">
                            <b-button type="is-primary" icon-left="plus" icon-pack="fas" @click="$emit('add-new', newCollectionName)"></b-button>
                          </p>
                        </b-field>
                    </form>
                  </b-dropdown-item>
                </template>
              </b-dropdown>
            </div>

            <div class="level-item mx-0">
              <b-tooltip label="Download">
                <b-button type="is-text" icon-left="download" icon-pack="fas" tag="a" :href="image.downloadUrl + '?dl=1'"></b-button>
              </b-tooltip>
            </div>

            <div class="level-item mx-0">
              <b-dropdown>
                <template #trigger>
                  <b-tooltip label="Copy link">
                    <b-button type="is-text" icon-left="link" icon-pack="fas"></b-button>
                  </b-tooltip>
                </template>

                <template #default>
                  <b-dropdown-item @click="$emit('copy-link')">
                    <span class="icon-text">
                      <b-icon icon="link" pack="fas"></b-icon>
                      <span>Copy link</span>
                    </span>
                  </b-dropdown-item>

                  <b-dropdown-item @click="$emit('copy-markdown')">
                    <span class="icon-text">
                      <b-icon icon="markdown" pack="fab"></b-icon>
                      <span>Copy as Markdown</span>
                    </span>
                  </b-dropdown-item>

                  <b-dropdown-item @click="$emit('copy-bbcode')"">
                    <span class="icon-text">
                      <b-icon icon="comment-dots" pack="fas"></b-icon>
                      <span>Copy as BBCode</span>
                    </span>
                  </b-dropdown-item>

                  <b-dropdown-item @click="$emit('copy-html')">
                    <span class="icon-text">
                      <b-icon icon="code" pack="fas"></b-icon>
                      <span>Copy as HTML</span>
                    </span>
                  </b-dropdown-item>
                </template>
              </b-dropdown>
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
                  <b-tooltip label="Edit image" key="edit">
                    <b-button type="is-text" icon-left="pencil-alt" icon-pack="fas" @click="$emit('edit')"></b-button>
                  </b-tooltip>
                </template>
              </div>

              <div class="level-item mx-0">
                <b-tooltip label="Delete image" type="is-danger">
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
