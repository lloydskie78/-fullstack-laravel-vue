<template>
    <div>
       <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Tags <Button @click="addModal=true"><Icon type="ios-add" /> Add Tag</Button></p>

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
							<tr>
								<td>1</td>
								<td class="_table_name">Manhattan's art center "Shed" opening ceremony</td>
								<td>Economy</td>
								<td>
                                    <Button type="info" size="small">Info</Button>
                                    <Button type="error" size="small">Info</Button>
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
    				    
						<Input v-model="data.tagName" placeholder="Add tag name" clearable />
 
						<div slot="footer">
							<Button type="default" @click="addModal=false">Close</Button>
							<Button type="info" @click="addTag" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding...' : 'Add Tag'}}</Button>
						</div>
    				</Modal>
			</div>
		</div>
    </div>
</template>

<script>
export default {
	data(){
		return{
			data : {
				tagName: ''
			},
			addModal : false,
			isAdding : false,
		}
	},
	methods : {
		 async addTag(){
			if(this.data.tagName.trim()	=='') return this.error('Tag name is required!')
			const res = await this.callApi('post', 'app/create_tag', this.data)
			alert(res.status)
			if(res.status===201){
				this.success('Tag added successfully')
				this.addModal = false
				alert("HEllow RODL!");
			}else{
				 this.swr();
			}
		}
	}
}
</script>