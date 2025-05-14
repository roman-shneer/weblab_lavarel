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
    go: function (url: string) { 
        window.location.href = url;
    },
    logout: function () { 
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        axiosPro.post('/logout', { csrf: csrfToken }, () => { 
            localStorage.removeItem('encrypt_pass');
            this.go("/login");
        });
    }
};

export default axiosPro