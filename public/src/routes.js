import CollectionDetails from './routes/CollectionDetails.js';
import Home from './routes/Home.js';
import ImageDetails from './routes/ImageDetails.js';
import Login from './routes/Login.js';
import Logout from './routes/Logout.js';
import Settings from './routes/Settings.js';
import UserDetails from './routes/UserDetails.js';
import UserList from './routes/UserList.js';


// Create the routes
const routes = [
  {path: '/', name: 'home', component: Home},
  {path: '/login', name: 'login', component: Login},
  {path: '/logout', name: 'logout', component: Logout},
  {path: '/settings', name: 'settings', component: Settings, meta: {requireLoggedIn: true}},
  {path: '/collections/:collectionId', name: 'collection', component: CollectionDetails},
  {path: '/images/:imageId', name: 'image', component: ImageDetails},
  {path: '/users', name: 'userList', component: UserList},
  {path: '/users/:userId', name: 'user', component: UserDetails},
];

// Export the routes
export default routes;
