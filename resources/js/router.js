import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

import firstpage from './components/pages/myFirstVuePage'
import newRoutePage from './components/pages/newRoutePage'
import hooks from './components/pages/basic/hooks.vue'
import methods from './components/pages/basic/methods.vue'


import home from './components/pages/home'
import tags from './admin/pages/tags'
import category from './admin/pages/category'

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
    }
] 

export default new Router({
    mode: 'history',
    routes
}) 