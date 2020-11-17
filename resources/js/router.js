import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

import firstpage from './components/pages/myFirstVuePage'
import newRoutePage from './components/pages/newRoutePage'
import hooks from './components/pages/basic/hooks.vue'
import methods from './components/pages/basic/methods.vue'
import usecom from './vuex/usecom'


import home from './components/pages/home'
import tags from './admin/pages/tags'
import category from './admin/pages/category'
import adminusers from './admin/pages/adminusers'
import login from './admin/pages/login'

const routes = [
    //project routes
    {
        path: '/',
        component: home
    },
    {
        path: '/tags',
        component: tags
    },
    {
        path: '/category',
        component: category
    },
    {
        path: '/testvuex',
        component: usecom
    },
    {
        path: '/adminusers',
        component: adminusers
    },
    {
        path: '/login',
        component: login
    }
] 

export default new Router({
    mode: 'history',
    routes
}) 