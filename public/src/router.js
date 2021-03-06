import routes from './routes.js';


// Create the router
const router = new VueRouter({
  mode: 'history',
  base: routerBasePath || '/',
  routes: routes,
});

// Add a redirect for authentication
router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requireLoggedIn))
  {
    // Check if a token is saved
    if (localStorage.getItem('accessToken') === null)
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
