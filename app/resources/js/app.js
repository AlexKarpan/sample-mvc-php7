
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Vue = require('vue');
window.BootstrapVue = require('bootstrap-vue');
window.VueCookie = require('vue-cookie');
Vue.use(BootstrapVue);
Vue.use(VueCookie);
