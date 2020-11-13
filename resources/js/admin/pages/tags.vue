<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
                <div
                    class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
                >
                    <p class="_title0">
                        Tags
                        <Button @click="addModal = true"
                            ><Icon type="ios-add" /> Add Tag</Button
                        >
                    </p>

                    <div class="_overflow _table_div">
                        <table class="_table">
                            <!-- TABLE TITLE -->
                            <tr>
                                <th>ID</th>
                                <th>Tag name</th>
                                <th>Created at</th>
                                <th>Action</th>
                            </tr>
                            <!-- TABLE TITLE -->

                            <!-- ITEMS -->
                            <tr
                                v-for="(tag, i) in tags"
                                :key="i"
                                v-if="tags.length"
                            >
                                <td>{{ tag.id }}</td>
                                <td class="_table_name">{{ tag.tagName }}</td>
                                <td>{{ tag.created_at }}</td>
                                <td>
                                    <Button
                                        type="info"
                                        size="small"
                                        @click="showEditModal(tag, i)"
                                        >Edit</Button
                                    >
                                    <Button
                                        type="error"
                                        size="small"
                                        @click="showDelModal(tag, i)"
                                        :loading="tag.isDeleting"
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
                    <Input
                        v-model="data.tagName"
                        placeholder="Add tag name"
                        clearable
                    />

                    <div slot="footer">
                        <Button type="default" @click="addModal = false"
                            >Close</Button
                        >
                        <Button
                            type="info"
                            @click="addTag"
                            :disabled="isAdding"
                            :loading="isAdding"
                            >{{ isAdding ? "Adding..." : "Add Tag" }}</Button
                        >
                    </div>
                </Modal>
                <!---- TAG EDITING MODAL -->
                <Modal
                    v-model="editModal"
                    title="Edit Tag"
                    :mask-closable="false"
                    :closable="false"
                >
                    <Input
                        v-model="editData.tagName"
                        placeholder="Edit tag name"
                        clearable
                    />

                    <div slot="footer">
                        <Button type="default" @click="editModal = false"
                            >Close</Button
                        >
                        <Button
                            type="info"
                            @click="editTag"
                            :disabled="isAdding"
                            :loading="isAdding"
                            >{{ isAdding ? "Editing..." : "Edit Tag" }}</Button
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
                tagName: ""
            },
            addModal: false,
            editModal: false,
            isAdding: false,
            tags: [],
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
        async addTag() {
            if (this.data.tagName.trim() == "")
                return this.e("Tag name is required!");
            const res = await this.callApi("post", "app/create_tag", this.data);
            if (res.status === 201) {
                this.tags.unshift(res.data);
                this.s("Tag added successfully");
                this.addModal = false;
                this.data.tagName = "";
            } else {
                if (res.status == 422) {
                    if (res.data.errors.tagName) {
                        this.i(res.data.errors.tagName[0]);
                    }
                } else {
                    this.swr();
                }
            }
        },
        async editTag() {
            if (this.editData.tagName.trim() == "")
                return this.e("Tag name is required!");
            const res = await this.callApi(
                "post",
                "app/edit_tag",
                this.editData
            );
            if (res.status === 200) {
                this.tags[this.index].tagName = this.editData.tagName;
                this.s("Tag edited successfully");
                this.editModal = false;
            } else {
                if (res.status == 422) {
                    if (res.data.errors.tagName) {
                        this.i(res.data.errors.tagName[0]);
                    }
                } else {
                    this.swr();
                }
            }
        },
        showEditModal(tag, index) {
            let obj = {
                id: tag.id,
                tagName: tag.tagName
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
        const res = await this.callApi("get", "app/get_tags");
        if (res.status === 200) {
            this.tags = res.data;
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
