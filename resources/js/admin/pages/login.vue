<template>
    <div>
        <div class="content">
            <div class="container">
                <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20 col-md-12">
                    <div class="login_header">
                        <h1>Login to the dashboard</h1>
                    </div>
                    <div class="space">
                        <Input type="email" v-model="data.email" placeholder="Email" />
                    </div>
                    <div class="space">
                        <Input type="password" v-model="data.password" placeholder="******" />
                    </div>
                    <div class="login_footer">
                        <Button type="primary" @click="login" :disabled="this.isLogging" :loading="isLogging">{{ isLogging ? 'Loading ...' : 'Login'}}</Button>
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
            data : {
                email: '',
                password: ''
            },
            isLogging: false
        }
    },
    methods : {
        async login(){
            if (this.data.email.trim() == "") return this.e("Email is required!");
            if (this.data.password.trim() == "") return this.e("Password is required!");
            if (this.data.password.length < 8) return this.e("Password must length must be 8");
            this.isLogging = true
            const res = await this.callApi('post', 'app/user_login', this.data)

            if(res.status === 200){
                window.location = "/"       
                this.s(res.data.msg)         
            }else{
                if(res.status === 401){
                    this.e(res.data.msg)
                }else if(res.status === 422){ //? This line is for the server error or the backend error
                    for (let i in res.data.errors) {
                        this.e(res.data.errors[i][0])
                    }
                }else{
                    this.swr()
                }
            }
            this.isLogging = false
        }
    }
}
</script>


<style scoped>
    ._1adminOverveiw_table_recent {
        margin: 0 auto;
        margin-top: 220px;
        width: 400px;
    }
    .login_footer{
        text-align: center;
    }
    .login_header{
        text-align: center;
        margin-bottom: 25px;
    }
</style>