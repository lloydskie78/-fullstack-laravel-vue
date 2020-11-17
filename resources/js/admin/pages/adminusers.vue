<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
                <div
                    class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
                >
                    <p class="_title0">
                        Users
                        <Button @click="addModal = true"
                            ><Icon type="ios-add" /> Add User</Button
                        >
                    </p>

                    <div class="_overflow _table_div">
                        <table class="_table">
                            <!-- TABLE TITLE -->
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Usertype</th>
                                <th>Created at</th>
                                <th>Action</th>
                            </tr>
                            <!-- TABLE TITLE -->

                            <!-- ITEMS -->
                            <tr
                                v-for="(user, i) in users"
                                :key="i"
                                v-if="users.length"
                            >
                                <td>{{ user.id }}</td>
                                <td class="_table_name">{{ user.fullName }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.userType }}</td>
                                <td>{{ user.created_at }}</td>
                                <td>
                                    <Button
                                        type="info"
                                        size="small"
                                        @click="showEditModal(user, i)"
                                        >Edit</Button
                                    >
                                    <Button
                                        type="error"
                                        size="small"
                                        @click="showDelModal(user, i)"
                                        :loading="user.isDeleting"
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
                    title="Add Tag"
                    :mask-closable="false"
                    :closable="false"
                >
                    <div class="space">
                        <Input type="text"
                        v-model="data.fullName"
                        placeholder="Fullname"
                        clearable
                    />
                    </div>
                    <div class="space">
                        <Input type="email"
                        v-model="data.email"
                        placeholder="Email"
                        clearable
                    />
                    </div>
                    <div class="space">
                        <Input type="password"
                        v-model="data.password"
                        placeholder="Password"
                        clearable
                    />
                    </div>
                    <div class="space">
                        <Select v-model="data.userType" placeholder="Select User Type">
                            <Option value="Admin">Admin</Option>
                            <Option value="Editor">Editor</Option>
                        </Select>
                    </div>
                    <div slot="footer">
                        <Button type="default" @click="addModal = false"
                            >Close</Button
                        >
                        <Button
                            type="primary"
                            @click="addUser"
                            :disabled="isAdding"
                            :loading="isAdding"
                            >{{ isAdding ? "Adding..." : "Add User" }}</Button
                        >
                    </div>
                </Modal>
                <!---- TAG EDITING MODAL -->
                <Modal
                    v-model="editModal"
                    title="Edit User"
                    :mask-closable="false"
                    :closable="false"
                >
                   <div class="space">
                        <Input type="text"
                        v-model="editData.fullName"
                        placeholder="Fullname"
                        clearable
                    />
                    </div>
                    <div class="space">
                        <Input type="email"
                        v-model="editData.email"
                        placeholder="Email"
                        clearable
                    />
                    </div>
                    <div class="space">
                        <Input type="password"
                        v-model="editData.password"
                        placeholder="Password"
                        clearable
                    />
                    </div>
                    <div class="space">
                        <Select v-model="editData.userType" placeholder="Select User Type">
                            <Option value="Admin">Admin</Option>
                            <Option value="Editor">Editor</Option>
                        </Select>
                    </div>
                    <div slot="footer">
                        <Button type="default" @click="editModal = false"
                            >Close</Button
                        >
                        <Button
                            type="info"
                            @click="editUser"
                            :disabled="isAdding"
                            :loading="isAdding"
                            >{{ isAdding ? "Editing..." : "Edit User" }}</Button
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
                fullName: '',
                email: '',
                password: '',
                userType: 'Admin',
            },
            addModal: false,
            editModal: false,
            isAdding: false,
            users: [],
            editData: {
                tagName: ""
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
        async addUser() {
            if (this.data.fullName.trim() == "")
                return this.e("Fullname is required!");
            if (this.data.email.trim() == "")
                return this.e("EMail is required!");
            if (this.data.password.trim() == "")
                return this.e("Password is required!");
            if (this.data.userType.trim() == "")
                return this.e("User type is required!");

            const res = await this.callApi("post", "app/create_user", this.data);
            if (res.status === 201) {
                this.tags.unshift(res.data);
                this.s("User added successfully");
                this.addModal = false;
                this.data.tagName = "";
            } else {
                if (res.status == 422) {
                    for(let i in res.data.errors){
                        this.e(res.data.errors[i][0])
                    }
                } else {
                    this.swr();
                }
            }
        },
        async editUser() {
            if (this.editData.fullName.trim() == "")
                return this.e("Fullname is required!");
            if (this.editData.email.trim() == "")
                return this.e("EMail is required!");
            if (this.editData.userType.trim() == "")
                return this.e("User type is required!");

            const res = await this.callApi(
                "post",
                "app/edit_user",
                this.editData
            );

            if (res.status === 200) {
                this.users[this.index] = this.editData;
                this.s("User edited successfully");
                this.editModal = false;
            } else {
                if (res.status == 422) {
                    for(let i in res.data.errors){
                        this.e(res.data.errors[i][0])
                    }
                } else {
                    this.swr();
                }
            }
        },
        showEditModal(user, index) {
            let obj = {
                id: user.id,
                fullName: user.fullName,
                email: user.email,
                userType: user.userType,
            };
            this.editData = obj;
            this.editModal = true;
            this.index = index;
        },
        async deleteTag() {
            this.isDeleting = true;
            const res = await this.callApi(
                "post",
                "app/delete_tag",
                this.deleteItem
            );
            if (res.status === 200) {
                this.tags.splice(this.delIndex, 1);
                this.w("A tag has been deleted.");
            } else {
                this.swr();
            }
            this.isDeleting = false;
            this.showDeleteModal = false;
        },
        showDelModal(tag, i) {
            const deleteModalObj = {
                showDeleteModal: true,
                deleteUrl: "app/delete_tag",
                data: tag,
                deletingIndex: i,
                isDeleted: false
            };
            this.$store.commit("setDeletingModalObj", deleteModalObj);
            console.log("delete method called");
            // this.deleteItem = tag
            // this.delIndex = i
            // this.showDeleteModal = true
        }
    },
    async created() {
        const res = await this.callApi("get", "app/get_users");
        if (res.status === 200) {
            this.users = res.data;
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
                this.tags.splice(obj.delIndex, 1);
            }
        }
    }
};
</script>
