<script setup lang="ts">
import NewForm from '../components/NewForm.vue';
import SearchForm from '../components/SearchForm.vue';
import EncText from '../components/encText.vue';
import Header from '../components/Header.vue';

import axiosPro from '../libraries/axiosPro';
import Helper from '../libraries/helper';
import Enc from "../libraries/Enc";
</script>
<script lang="ts">

export default {
    props: {

        statuses: Array,
        encrypted: Boolean,
        encrypted_example: String
    },
    data() {
        let encrypt_pass = localStorage.getItem('encrypt_pass');
        if (encrypt_pass == null) {
            encrypt_pass = '';
        }
        let encryptionStatus = this.encrypted ? 1 : 0;
        if (encryptionStatus == 1 && encrypt_pass.length > 0 && this.encrypted_example && this.encrypted_example.length > 0) {
            encryptionStatus = (Enc.decrypt(this.encrypted_example, encrypt_pass) == 'test') ? 2 : 1;
        }
        return {
            max_id: 0,
            items: [] as Array<{ id: number;[key: string]: any }>,
            subitems: [] as any,
            searchFormAttributes: {
                exp_number: '',
                title: '',
                date: '',
                status: '0',
                source: '',
            },
            message: '',
            flashMessage: '',
            showForm: false,
            editItem: {},
            encrypt_pass: encrypt_pass,
            encrypt_columns: ['title', 'description', 'source'],
            encryptionStatus: encryptionStatus
        };
    },
    methods: {

        renderStatus(status: number) {

            if (typeof this.statuses != 'undefined' && typeof this.statuses[status] != 'undefined') {
                return this.statuses[status];
            } else {
                return "unknow " + status;
            }
        },

        setItems(items: any) {
            this.items = items;
        },

        setMax(maxnum: number) {
            this.max_id = maxnum;
        },

        closeForm() {
            this.editItem = {};
            this.subitems = [];
            this.showForm = false;
        },

        saveForm(args: any) {
            const params = Object.assign({}, args);
            if (this.is_encryptable()) {
                this.encrypt_columns.forEach(k => {
                    params[k] = Enc.encrypt(params[k], this.encrypt_pass);
                });
            }
            axiosPro.post('/task', params, () => {
                this.closeForm();
                this.search();
            });
        },

        is_encryptable() {          
            return this.encryptionStatus == 2;
        },

        editItemForm(exp_number: number) {
            axiosPro.get('/tasks', { exp_number: exp_number }, (response: any) => {
                if (this.is_encryptable()) {
                    response.data.items.forEach((item: any, ind: number) => {
                        this.encrypt_columns.forEach(key => {
                            response.data.items[ind][key] = Enc.decrypt(item[key], this.encrypt_pass);
                        });
                    });
                }
                this.editItem = response.data.items[0];
                this.subitems = response.data.items;
                this.showForm = true;


            });
        },

        addNewForm() {
            if (this.encryptionStatus == 1) { 
                this.setFlashMessage("You have to unlock data first, please set password.");
                return;
            }
            this.editItem = {
                exp_number: this.max_id,
                title: '',
                description: '',
                datetime: Helper.date() + ' ' + Helper.time(),
                source: 0,
                status: 0
            }
            this.showForm = true
        },

        setDate(datetime: string) {
            this.searchFormAttributes.date = Helper.date(datetime);
        },

        setStatus(status: string) {
            this.searchFormAttributes.status = status;
        },

        setId(exp_number: string) {
            this.searchFormAttributes.exp_number = exp_number;
        },

        setName(evt: any) {
            if (this.encryptionStatus!=1) {
                this.searchFormAttributes.title = evt.target.innerText;
            }
        },

        search() {
            const args = this.searchFormAttributes;
            if (args.exp_number == '' && args.title == '' && args.date == '' && args.status == '' && args.source == '') {
                this.message = "Nothing to search";
            } else {
                this.message = "searching..."
            }

            const params = Object.assign({}, args);
            if (this.is_encryptable()) {
                if (params.title.length > 0) {
                    params.title = Enc.encrypt(params.title, this.encrypt_pass);
                }
                if (params.source.length > 0) {
                    params.source = Enc.encrypt(params.source, this.encrypt_pass);
                }
            }
            axiosPro.get('/tasks_grouped', params, (response: any) => {
                this.max_id = response.data.max_id.toString();
                this.message = "found " + response.data.items.length + " items";
                this.items = response.data.items;
            });
        },

        setFlashMessage(msg: string) {
            this.flashMessage = msg;
            setTimeout(() => {
                this.flashMessage = '';
            }, 5000)
        },

        setEncryptPass(password: string) {
            if (password == '') {
                this.encrypt_pass = password;
                localStorage.setItem('encrypt_pass', this.encrypt_pass);
                this.encryptionStatus = 1;
                return;
            }
            
            if (this.encrypted_example) {
                if (Enc.decrypt(this.encrypted_example, password) == 'test') {
                    console.log('set correct password', password, this.encrypted_example, Enc.decrypt(this.encrypted_example, this.encrypt_pass));
                    this.encrypt_pass = password;
                    this.encryptionStatus = 2;
                    localStorage.setItem('encrypt_pass', this.encrypt_pass);
                } else {
                    this.encryptionStatus = 1;
                    this.setFlashMessage('Its wrong password, cannot encode!')
                }

            }
        },
        cutDescription(text: string) { 
            if (text.length > 50) { 
                text = text.substring(0, 50) + '...';
            }
            console.log(text, text.length);
            return text;
        }

    },

    components: {
        EncText,
        Header,
        NewForm,
        SearchForm
    },

    mounted() {
        this.search();
    }
}
</script>
<template>
    <Header :encrypt_pass="encrypt_pass" :encryptionStatus="encryptionStatus" @setEncryptPass="setEncryptPass"></Header>
    <div v-if="flashMessage.length > 0" class="flashMessage">{{ flashMessage }}</div>
    <div class="items-list">
        <SearchForm :statuses="statuses" :searchFormAttributes="searchFormAttributes" @search="search" />
        <NewForm :editItem="editItem" :subitems="subitems" :statuses="statuses" v-if="showForm" @closeForm="closeForm"
            @search="search" @saveForm="saveForm" />
        <br />
        <div class="middle-box">
            <div class="middle-btn-box">
                <span class="message">{{ message }}</span>
                <button @click="addNewForm" class="btn new_btn">New experiment (Next Exp. Id: {{ max_id }})</button>    
            </div>
            <table :border="1" id="list-table">
                <tbody>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Title</th>
                        <th class="mobile-hide">Description</th>
                        <th class="mobile-hide">Source</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr v-for="item in items" v-bind:key="item.id">
                        <td>
                            <a href="javascript://" @click="setId(item.exp_number)">                        
                                <EncText :val="item.exp_number"  :encryptionStatus="encryptionStatus"></EncText>
                            </a>
                        </td>
                        <td>
                            <a href="javascript://" @click="setDate(item.datetime)">                        
                                <EncText :val="Helper.renderTimeDate(item.datetime, 'date')"  :encryptionStatus="encryptionStatus"></EncText>
                            </a>
                        </td>
                        <td>
                            <a href="javascript://" @click="setName">
                                <EncText :val="item.title"  :encryptionStatus="encryptionStatus" :encrypt_pass="encrypt_pass"></EncText>
                            </a>
                        </td>
                        <td class="mobile-hide">
                            <a href="javascript://">
                                <EncText :val="cutDescription(item.description)"  :encryptionStatus="encryptionStatus" :encrypt_pass="encrypt_pass"></EncText>
                            </a>
                        </td>
                       
                        <td class="mobile-hide">
                            <a href="javascript://" @click="">
                                <EncText :val="item.source"  :encryptionStatus="encryptionStatus" :encrypt_pass="encrypt_pass"></EncText>                        
                            </a>
                        </td>
                        <td>
                            <a href="javascript://" @click="setStatus(item.status)">
                                <EncText :val="renderStatus(item.status)"  :encryptionStatus="encryptionStatus"></EncText>                        
                            </a>
                        </td>
                        <td class="align-right">
                            <a href="javascript://" @click="editItemForm(item.exp_number)" class="clickable" v-if="encryptionStatus!=1">
                                <span class="material-symbols-outlined">settings</span>
                                ({{ item.amount }})
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        

    </div>
</template>
<style>
body {
    padding: 0;
    margin: 0;
}

.btn {
    background: #4CAF50;
    /* Green */
    border: none;
    color: white;
    padding: 3px 15px;
    border-radius: 5px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    box-shadow: 1px 1px 5px #888888;
}
</style>
<style scoped>
.message{
    float:left;
    margin-left:15px;
}
.new_btn{
    float:right;
    margin-right:15px;
}
.flashMessage {
    border: solid red 1px;
    padding: 1em;
    position: fixed;
    top: 50%;
    transform: translate(-50%, -50%);
    left: 50%;
    background: brown;
    color: white;
    font-weight: bold;
    border-radius: 1em;
    box-shadow: 1px 1px 5px #888888;
    z-index: 1;
}

.items-list {
    margin-top: 30px;
    position: absolute;
    top:0;
    width:100%;
}
.middle-box{
    width:60vw;
    margin:0 auto;
}
#list-table {
    margin-top: 15px;
    width: 100%;
    border-collapse: collapse;
    font-size: 150%;
}

#list-table tr {
    background: #f2f2f2;
    border-bottom: solid 1px #ddd;
}

#list-table th {
    text-align: left;
    padding: 1px;
}

#list-table td {
    padding: 1px;
}
#list-table td:nth-child(1){
    width:5%;
}
#list-table td:nth-child(2){
    width:10%;
}

#list-table td:nth-child(6){
    width:5%;
}
#list-table td:last-child{
    
}
.clickable {
    color: blue;
}

.align-right {
    text-align: right;
}
.middle-btn-box{
    height: 25px;
}
@media only screen and (max-width: 600px) {
    .new_btn,.message{
        margin:0;
    }    
    .middle-box{
        width:100%;
    }
    #list-table {
        font-size: 100%;
        width: 100%;
    }
    .mobile-hide{
        display: none;
    }

    
    #list-table td:nth-child(1){
        width:5%;
    }
    #list-table td:nth-child(2){
        width:15%;
    }
    #list-table td:nth-child(3){
        width:15%;
    }
    #list-table td:nth-child(4){
        width:5%;
    }
    #list-table td:nth-child(5){
        width:5%;
    }
    #list-table td:last-child{
        width:10%;
    }

}
</style>