import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    uid: undefined
  },
  mutations: {
    setUniqueId(state, uniqueId) {
      state.uid = uniqueId;
    }
  },
  actions: {
  },
  modules: {
  }
})
