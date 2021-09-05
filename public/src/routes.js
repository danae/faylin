import CollectionDetails from './routes/CollectionDetails.js';
import Home from './routes/Home.js';
import ImageDetails from './routes/ImageDetails.js';
import Login from './routes/Login.js';
import Logout from './routes/Logout.js';
import Settings from './routes/Settings.js';
import UserDetails from './routes/UserDetails.js';
import UserDetailsShort from './routes/UserDetailsShort.js';


// Create the routes
const routes = [
  // Home route
  {path: '/', name: 'home', component: Home},

  // Session routes
  {path: '/login', name: 'login', component: Login},
  {path: '/logout', name: 'logout', component: Logout},

  // Model routes
  {path: '/c/:collectionId', name: 'collection', component: CollectionDetails},
  {path: '/i/:imageId', name: 'image', component: ImageDetails},
  {path: '/u/:userId', name: 'user', component: UserDetails},
  {path: '/~:userName', name: 'userShort', component: UserDetailsShort},

  // Settings routes
  {path: '/settings/:page*', name: 'settings', component: Settings, meta: {requireLoggedIn: true}},
];

// Export the routes
export default routes;
