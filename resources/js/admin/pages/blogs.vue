<template>
  <div>
    <div class="content">
      <div class="container-fluid">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div
          class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
        >
          <p class="_title0">
            Blogs
            <Button @click="$router.push('/createBlog')">
              <Icon type="ios-add" /> Create Blog
            </Button>
          </p>

          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Tags</th>
                <th>Views</th>
                <th>Action</th>
              </tr>
              <!-- TABLE TITLE -->

              <!-- ITEMS -->
              <tr v-for="(blog, i) in blogs" :key="i" v-if="blogs.length">
                <td>{{ blog.id }}</td>
                <td class="_table_name">{{ blog.title }}</td>
                <td>
                  <span v-for="(c, j) in blog.cat" v-if="blog.cat.length"
                    ><Tag color="default">{{ c.categoryName }}</Tag></span
                  >
                </td>
                <td>
                  <span v-for="(t, j) in blog.tag" v-if="blog.tag.length"
                    ><Tag color="default">{{ t.tagName }}</Tag></span
                  >
                </td>
                <td>{{ blog.views }}</td>
                <td>
                  <Button type="success" size="small">View</Button>
                  <Button
                    type="info"
                    size="small"
                    @click="$router.push(`/editblog/${blog.id}`)"
                    v-if="isUpdatePermitted"
                    >Edit</Button
                  >
                  <Button
                    type="error"
                    size="small"
                    v-if="isDeletePermitted"
                    @click="showDelModal(blog, i)"
                    :loading="blog.isDeleting"
                    >Delete</Button
                  >
                </td>
              </tr>
              <!-- ITEMS -->
            </table>
          </div>
        </div>
        <Page
          :total="pageInfo.total"
          :current="pageInfo.current_page"
          :page-size="parseInt(pageInfo.per_page)"
          @on-change="getBlogData"
          v-if="pageInfo"
        />

        <deleteModal></deleteModal>
      </div>
    </div>
  </div>
</template>

<script>
import deleteModal from "../components/deleteModal.vue";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      isAdding: false,
      tags: [],
      index: -1,
      showDeleteModal: false,
      isDeleting: false,
      deleteItem: {},
      delIndex: -1,
      blogs: [],
      total: 1,
      pageInfo: null,
    };
  },
  methods: {
    showDelModal(tag, i) {
      this.delIndex = i;
      const deleteModalObj = {
        showDeleteModal: true,
        deleteUrl: "app/delete_blog",
        data: tag,
        deletingIndex: i,
        isDeleted: false,
        msg: "Are you sure you want to delete this blog?",
        successMsg: "Blog has been deleted successfully",
      };
      this.$store.commit("setDeletingModalObj", deleteModalObj);
      console.log("delete method called");
      // this.deleteItem = tag
      // this.delIndex = i
      // this.showDeleteModal = true
    },
    async getBlogData(page = 1) {
      const res = await this.callApi(
        "get",
        `app/blogsdata?page=${page}&total=${this.total}`
      );
      if (res.status === 200) {
        this.blogs = res.data.data;
        this.pageInfo = res.data;
      } else {
        this.swr();
      }
    },
  },
  async created() {
    this.getBlogData();
  },
  components: {
    deleteModal,
  },
  computed: {
    ...mapGetters(["getDeleteModalObj"]),
  },
  watch: {
    getDeleteModalObj(obj) {
      console.log(obj);
      if (obj.isDeleted) {
        this.blogs.splice(this.delIndex, 1);
      }
    },
  },
};
</script>
