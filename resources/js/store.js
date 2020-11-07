import vue from 'vue'
import vuex from 'vuex'
vue.use(vuex)

export default new vuex.Store({
    state: {
        counter: 1000
    },

    mutations : {
        changeTheCounter(state, data){
            state.counter += data
        }
    }
})