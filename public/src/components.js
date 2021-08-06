import ImageDetails from './components/ImageDetails.js';
import ImageDetailsEditPanel from './components/ImageDetailsEditPanel.js';
import ImageDetailsSharePanel from './components/ImageDetailsSharePanel.js';
import ImageThumbnail from './components/ImageThumbnail.js';
import ImageThumbnailList from './components/ImageThumbnailList.js';
import UploadForm from './components/UploadForm.js';
import UserDetails from './components/UserDetails.js';
import UserThumbnail from './components/UserThumbnail.js';
import UserThumbnailList from './components/UserThumbnailList.js';


// Create the components
const components =  {
  ImageDetails,
  ImageDetailsEditPanel,
  ImageDetailsSharePanel,
  ImageThumbnail,
  ImageThumbnailList,
  UploadForm,
  UserDetails,
  UserThumbnail,
  UserThumbnailList,
}

// Register the components
Object.keys(components).forEach(key => Vue.component(key, components[key]));

// Export the components
export default components;
