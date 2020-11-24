<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
               	<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Create blog</p>
					
					<div class="_overflow _table_div blog_editor">

                             <editor
                                ref="editor"
                                autofocus
                                holder-id="codex-editor"
                                save-button-id="save-button"
                                :init-data="initData"
                                @save="onSave"
								:config="config"
							/>


					</div>
					<!-- <div class="_input_field">
						<Select  filterable multiple placeholder="Select category" v-model="data.category_id">
							<Option v-for="(c, i) in category" :value="c.id" :key="i">{{ c.categoryName }}</Option>
						</Select>
					</div> -->
                    <div class="_input_field">
						 <Input type="text" v-model="data.title" placeholder="Title" />
					 </div>

					 <div class="_input_field">
						 <Button @click="save" :loading="isCreating" :disabled="isCreating">{{isCreating ? 'Please wait...' : 'Create blog'}}</Button>
					 </div>

				</div>


            </div>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            config: {
                
			},
            initData: null,
            data: {
				title : '',
				post : '',
				post_excerpt : '',
				metaDescription : '',
				category_id : [],
				tag_id : [],
				jsonData: null

			},
			articleHTML: '',
			category : [],
			tag : [],
			isCreating: false,


		}
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
        onSave(response){
            console.log('Response on save', response)
        },
        async save(){
            $refs.editor.save()
        }
    },
};
</script>


<style>
	.blog_editor {
		width: 717px;
		margin-left: 160px;
		padding: 4px 7px;
		font-size: 14px;
		border: 1px solid #dcdee2;
		border-radius: 4px;
		color: #515a6e;
		background-color: #fff;
		background-image: none;
		z-index:  -1;
	}
	.blog_editor:hover {
		border: 1px solid #57a3f3;
	}

	._input_field{
		margin: 20px 0 20px 160px;
    	width: 717px;
	}
</style>