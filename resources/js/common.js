
export default {
    data(){
        return {

        }
    },
    methods: {
        callApi(method, url, dataObj){
            try {
                axios({
                    method: method,
                    url: url,
                    data: dataObj
                });
            } catch (error) {
                return error.response
            }

        },
        info (desc, title="Hey") {
            this.$Notice.info({
                title: title,
                desc: desc
            });
        },
        success (desc, title="Great!") {
            this.$Notice.success({
                title: title,
                desc: desc
            });
        },
        warning (desc, title="Opps!") {
            this.$Notice.warning({
                title: title,
                desc: desc
            });
        },
        error(desc, title="Oops!") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        }, 
        swr (desc = 'Something went wrong! Please try again.', title="Hey") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        }
    },
    
}