import axios from 'axios';
const axiosPro = {
    get: function (url:string, params:any, callback:any) {
        axios
            .get(url + '?' + new URLSearchParams(params).toString())
            .then(callback);
    },
    post: function (url:string, params:any, callback:any) {
        axios.post(url, params)
            .then(callback)
    },
    put: function (url:string, params:any, callback:any) {
        axios.put(url, params)
            .then(callback)
    },
    delete: function (url:string, params:any, callback:any) {
        axios
            .delete(url + '?' + new URLSearchParams(params).toString())
            .then(callback)
    },
};

export default axiosPro