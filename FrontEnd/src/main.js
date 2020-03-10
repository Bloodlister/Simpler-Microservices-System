import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios';
import VueAxios from 'vue-axios';
import {getCookie} from "./utilities";

Vue.config.productionTip = false;
Vue.use(VueAxios, axios);

axios.defaults.withCredentials = true;
axios.interceptors.request.use(function(config) {
  config.headers.Receiver = getCookie('uid');

  return config;
});

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app');
