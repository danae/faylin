import routes from './routes.js';


// Create the router
const router = new VueRouter({
  mode: 'history',
  base: '/php/faylin-slim/',
  routes: routes,
});

// Add a redirect for authentication
router.beforeEach((to, from, next) =>
{
  if (to.matched.some(record => record.meta.requiresAuth))
  {
    // Check if a token is saved
    if (localStorage.getItem('token') === null)
      next({path: '/login', query: {redirect: to.fullPath}});
    else
      next();
  }
  else
  {
    next();
  }
});

// Export the router
export default router;
