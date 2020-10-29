import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

import firstpage from './components/pages/myFirstVuePage'
import newRoutePage from './components/pages/newRoutePage'
import hooks from './components/pages/basic/hooks.vue'
import methods from './components/pages/basic/methods.vue'
import home from './components/pages/home.vue'

const routes = [
    //project routes
    {
        path: '/',
        component: home
    },
    //test routes
    {
        path: '/my-new-vue-route',
        component: firstpage
    }, 
    {
        path: '/new-route',
        component: newRoutePage
    },
    //vue hooks
    {
        path: '/hooks',
        component: hooks
    },
    //more basics
    {
        path: '/methods',
        component: methods
    },
] 

export default new Router({
    mode: 'history',
    routes
}) 