import vue from 'vue'
import vuex from 'vuex'
vue.use(vuex)

export default new vuex.Store({
    state: {
        counter: 1000,
        deleteModalObj : {
            showDeleteModal: false,
            deleteUrl: '',
            data: null,
            deletingIndex: -1,
            isDeleted: false
        },
        user: false,
    },
    getters : {
        getCounter(state){
            return state.counter
        },
        getDeleteModalObj(state){
            return state.deleteModalObj
        },
    },
    mutations : {
        changeTheCounter(state, data){
            state.counter += data
        },
        setDeleteModal(state, data){

            const deleteModalObj = {
                showDeleteModal: false,
                deleteUrl: '', 
                data: null,
                deletingIndex: -1,
                isDeleted: data
            }

            state.deleteModalObj = deleteModalObj

        },
        setDeletingModalObj(state, data){
            state.deleteModalObj = data
        },
        updateUser(state, data){
            state.user = data
        }
    },
    actions : {
        changeCounterAction({commit}, data){
            commit('changeTheCounter', data)
        }
    }
})