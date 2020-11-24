require('./bootstrap')
window.Vue = require('vue')
import router from './router'
import ViewUI from 'view-design'
import store from './store'
import 'view-design/dist/styles/iview.css'
Vue.use(ViewUI);
import common from './common'
Vue.mixin(common)

import Editor from 'vue-editor-js'

Vue.use(Editor)

Vue.component('mainapp', require('./components/mainapp.vue').default)
const app = new Vue({
    el: '#app',
    router,
    store
})






                        //! REMEMBER!
//? app.js is the main entry point of vue in our laravel project