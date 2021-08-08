import Home from './routes/Home.js';
import ImageView from './routes/ImageView.js';
import Login from './routes/Login.js';
import Logout from './routes/Logout.js';
import Settings from './routes/Settings.js';
import UserList from './routes/UserList.js';
import UserView from './routes/UserView.js';


// Create the routes
const routes = [
  {path: '/', name: 'home', component: Home},
  {path: '/login', name: 'login', component: Login},
  {path: '/logout', name: 'logout', component: Logout},
  {path: '/settings', name: 'settings', component: Settings, meta: {requireLoggedIn: true}},
  {path: '/images/:imageId', name: 'imageView', component: ImageView},
  {path: '/users', name: 'userList', component: UserList},
  {path: '/users/:userId', name: 'userView', component: UserView},
];

// Export the routes
export default routes;
