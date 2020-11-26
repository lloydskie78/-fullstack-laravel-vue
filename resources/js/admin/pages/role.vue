<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
                <div
                    class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
                >
                    <p class="_title0">
                        Role Management
                        <Button @click="addModal = true" v-if="isWritePermitted">
                            <Icon type="ios-add" /> Add New Role
                        </Button>
                    </p>

                    <div class="_overflow _table_div">
                        <table class="_table">
                            <!-- TABLE TITLE -->
                            <tr>
                                <th>ID</th>
                                <th>Role Type</th>
                                <th>Created at</th>
                                <th>Action</th>
                            </tr>
                            <!-- TABLE TITLE -->

                            <!-- ITEMS -->
                            <tr
                                v-for="(role, i) in roles"
                                :key="i"
                                v-if="roles.length"
                            >
                                <td>{{ role.id }}</td>
                                <td class="_table_name">{{ role.rolename }}</td>
                                <td>{{ role.created_at }}</td>
                                <td>
                                    <Button
                                        type="info"
                                        size="small"
                                        v-if="isUpdatePermitted"
                                        @click="showEditModal(role, i)"
                                        >Edit</Button
                                    >
                                    <Button
                                        type="error"
                                        size="small"
                                        v-if="isDeletePermitted"
                                        @click="showDelModal(role, i)"
                                        :loading="role.isDeleting"
                                        >Delete</Button
                                    >
                                </td>
                            </tr>
                            <!-- ITEMS -->
                        </table>
                    </div>
                </div>

                <!-- TAG ADDING MODAL -->
                <Modal
                    v-model="addModal"
                    title="Add Role"
                    :mask-closable="false"
                    :closable="false"
                >
                    <Input
                        v-model="data.roleName"
                        placeholder="Role name"
                        clearable
                    />

                    <div slot="footer">
                        <Button type="default" @click="addModal = false"
                            >Close</Button
                        >
                        <Button
                            type="info"
                            @click="add"
                            :disabled="isAdding"
                            :loading="isAdding"
                            >{{ isAdding ? "Adding..." : "Add Role" }}</Button
                        >
                    </div>
                </Modal>
                <!---- TAG EDITING MODAL -->
                <Modal
                    v-model="editModal"
                    title="Edit Role"
                    :mask-closable="false"
                    :closable="false"
                >
                    <Input
                        v-model="editData.roleName"
                        placeholder="Edit role name"
                        clearable
                    />

                    <div slot="footer">
                        <Button type="default" @click="editModal = false"
                            >Close</Button
                        >
                        <Button
                            type="info"
                            @click="edit"
                            :disabled="isAdding"
                            :loading="isAdding"
                            >{{ isAdding ? "Editing..." : "Edit Role" }}</Button
                        >
                    </div>
                </Modal>

                <!--- TAG DELETING MODAL-->

                <!-- <Modal v-model="showDeleteModal" width="360">
    				    <p slot="header" style="color:#f60;text-align:center">
    				        <Icon type="ios-information-circle"></Icon>
    				        <span>Delete confirmation</span>
    				    </p>
    				    <div style="text-align:center">
    				        <p>Are you sure you want to delete this tag?</p>
    				    </div>
    				    <div slot="footer">
    				        <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteTag">Delete</Button>
    				    </div>
    				</Modal> -->
                <deleteModal></deleteModal>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import deleteModal from "../components/deleteModal.vue";

export default {
    data() {
        return {
            data: {
                roleName: ""
            },
            addModal: false,
            editModal: false,
            isAdding: false,
            roles: [],
            editData: {
                roleName: ""
            },
            index: -1,
            showDeleteModal: false,
            isDeleting: false,
            deleteItem: {},
            delIndex: -1,
            websiteSettings: []
        };
    },
    methods: {
        async add() {
            if (this.data.roleName.trim() == "")
                return this.e("Role name is required!");
            const res = await this.callApi("post", "app/create_role", this.data);
            if (res.status === 201) {
                this.roles.unshift(res.data);
                this.s("Role added successfully");
                this.addModal = false;
                this.data.roleName = "";
            } else {
                if (res.status == 422) {
                    if (res.data.errors.roleName) {
                        this.i(res.data.errors.roleName[0]);
                    }
                } else {
                    this.swr();
                }
            }
        },
        async edit() {
            console.log('This is edit!')
            console.log(this.editData.roleName.trim())
            if (this.editData.roleName.trim() == "")
                return this.e("Role name is required!");
            const res = await this.callApi(
                "post",
                "app/edit_role",
                this.editData
            );

            console.log('Hello from here')
            if (res.status === 200) {
                this.roles[this.index].rolename = this.editData.roleName;
                this.s("Role edited successfully");
                this.editModal = false;
            } else {
                if (res.status == 422) {
                    if (res.data.errors.roleName) {
                        this.i(res.data.errors.roleName[0]);
                    }
                } else {
                    this.swr();
                }
            }
        },
        async delete() {
            this.isDeleting = true;
            const res = await this.callApi(
                "post",
                "app/delete_role",
                this.deleteItem
            );
            if (res.status === 200) {
                this.roles.splice(this.delIndex, 1);
                this.w("A role has been deleted.");
            } else {
                this.swr();
            }
            this.isDeleting = false;
            this.showDeleteModal = false;
        },
        showEditModal(role, index) {
            console.log('Hello from editModal')
            console.log(role)

            let obj = { 
                id: role.id,
                roleName: role.rolename
            };
           
            this.editData = obj;
            this.editModal = true;
            this.index = index;


        },
        showDelModal(role, i) {
            const deleteModalObj = {
                showDeleteModal: true,
                deleteUrl: "app/delete_role",
                data: role,
                deletingIndex: i,
                isDeleted: false
            };
            this.$store.commit("setDeletingModalObj", deleteModalObj);
            console.log("delete method called");
            // this.deleteItem = role
            // this.delIndex = i
            // this.showDeleteModal = true
        }
    },
    async created() {
        const res = await this.callApi("get", "app/get_roles");
        if (res.status === 200) {
            this.roles = res.data;
        } else {
            this.swr();
        }
    },
    components: {
        deleteModal
    },
    computed: {
        ...mapGetters(["getDeleteModalObj"])
    },
    watch: {
        getDeleteModalObj(obj) {
            console.log(obj);
            if (obj.isDeleted) {
                this.roles.splice(this.delIndex, 1);
            }
        }
    }
};
</script>
