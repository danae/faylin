import CollectionDetails from './components/CollectionDetails.js';
import CollectionDetailsButtons from './components/CollectionDetailsButtons.js';
import CollectionDetailsEditPanel from './components/CollectionDetailsEditPanel.js';
import CollectionThumbnail from './components/CollectionThumbnail.js';
import CollectionThumbnailList from './components/CollectionThumbnailList.js';
import ImageDetails from './components/ImageDetails.js';
import ImageDetailsButtons from './components/ImageDetailsButtons.js';
import ImageDetailsEditPanel from './components/ImageDetailsEditPanel.js';
import ImageThumbnail from './components/ImageThumbnail.js';
import ImageThumbnailList from './components/ImageThumbnailList.js';
import SettingsDeleteAccountPanel from './components/SettingsDeleteAccountPanel.js';
import SettingsSessionsPanel from './components/SettingsSessionsPanel.js';
import SettingsUpdateEmailPanel from './components/SettingsUpdateEmailPanel.js';
import SettingsUpdatePasswordPanel from './components/SettingsUpdatePasswordPanel.js';
import SettingsUpdateProfilePanel from './components/SettingsUpdateProfilePanel.js';
import SettingsUserAccount from './components/SettingsUserAccount.js';
import SettingsUserProfile from './components/SettingsUserProfile.js';
import SettingsUserSessions from './components/SettingsUserSessions.js';
import UploadForm from './components/UploadForm.js';
import UserDetails from './components/UserDetails.js';
import UserThumbnail from './components/UserThumbnail.js';
import UserThumbnailList from './components/UserThumbnailList.js';


// Create the components
const components =  {
  CollectionDetails,
  CollectionDetailsButtons,
  CollectionDetailsEditPanel,
  CollectionThumbnail,
  CollectionThumbnailList,
  ImageDetails,
  ImageDetailsButtons,
  ImageDetailsEditPanel,
  ImageThumbnail,
  ImageThumbnailList,
  SettingsDeleteAccountPanel,
  SettingsSessionsPanel,
  SettingsUpdateEmailPanel,
  SettingsUpdatePasswordPanel,
  SettingsUpdateProfilePanel,
  SettingsUserAccount,
  SettingsUserProfile,
  SettingsUserSessions,
  UploadForm,
  UserDetails,
  UserThumbnail,
  UserThumbnailList,
}

// Register the components
Object.keys(components).forEach(key => Vue.component(key, components[key]));

// Export the components
export default components;
