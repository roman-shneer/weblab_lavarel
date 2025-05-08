<script setup lang="ts">  
//import { ref } from 'vue';  
//import { Link } from '@inertiajs/vue3';  
import { render } from 'vue';
import NewForm from '../components/NewForm.vue';
import SearchForm from '../components/SearchForm.vue';
import axiosPro from '../components/axiosPro.js';
import Helper from '../components/helper.js';
</script>
<script lang="ts">
export default {
props: {
    
    statuses: Array
},
    data() {
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
            showNewForm: false,
            showStatusForm: false,            
            editItem: {}
    };
},
    methods: {

        renderStatus(status:number) {

            if (typeof this.statuses!='undefined' && typeof this.statuses[status] != 'undefined') {
                return this.statuses[status];
            } else {
                return "unknow " + status;
            }
        },

        setItems(items: any) {
            console.log('setItems', items);
            this.items = items;
        },

        setMax(maxnum:number) {
            this.max_id = maxnum;
        },

        closeForm() {
            this.editItem = {};
            this.subitems = [];
            this.showNewForm = false;
        },

        editItemForm(exp_number:number) {            
            axiosPro.get('/tasks', { exp_number: exp_number }, (response: any) => {               
                this.editItem = response.data.items[0];
                this.subitems = response.data.items;
                this.showNewForm = true;
            });
        },
        
        addNewForm() {
            this.editItem = {
                exp_number: this.max_id,
                title: '',
                description: '',
                datetime: Helper.date() + ' ' + Helper.time(),
                source: 0,
                status: 0
            }
            this.showNewForm = true
        },

        setDate(datetime: string) {
            this.searchFormAttributes.date = Helper.date(datetime);            
        },

        setStatus(status:string) {
            this.searchFormAttributes.status = status;            
        },

        setId(exp_number:string) {
            this.searchFormAttributes.exp_number = exp_number;
        },

        setName(title:string) {
            this.searchFormAttributes.title = title;
        },

        search() {
            const args = this.searchFormAttributes;
            if (args.exp_number == '' && args.title == '' && args.date == '' && args.status == '' && args.source == '') {
                this.message = "Nothing to search";
            } else {
                this.message = "searching..."
            }
            
            axiosPro.get('/tasks_grouped', args,(response:any) => {
                this.max_id = response.data.max_id.toString();
                this.message = "found " + response.data.items.length + " items";
                this.items = response.data.items;                               
            });
        },
    },
    
    components: {
        NewForm,       
        SearchForm
    },
        
    mounted() {       
        this.search()              
    }
}
</script>
<template>  
    <div class="items-list">
    <h1>Web Lab</h1>
    <SearchForm :statuses="statuses" :searchFormAttributes="searchFormAttributes" @search="search"/>
    <NewForm :editItem="editItem" :subitems="subitems" :statuses="statuses" v-if="showNewForm"  @closeForm="closeForm" @search="search" />
    <br/>
    <div>{{message}}</div>
    <button @click="addNewForm" class="btn">New experiment</button>
    <table :border="1" id="list-table">
    <tbody>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Title</th>       
            <th>Status</th>
            
            <th>Next Id:{{max_id}}</th>
        </tr>
        <tr v-for="item in items" v-bind:key="item.id">
            <td><a href="javascript://"  @click="setId(item.exp_number)">{{item.exp_number}} ({{item.amount}})</a></td>
            <td><a href="javascript://" @click="setDate(item.datetime)">{{Helper.renderTimeDate(item.datetime)}}</a></td>
            <td><a href="javascript://"  @click="setName(item.title)">{{item.title}}</a></td>                     
            <td><a href="javascript://"  @click="setStatus(item.status)">{{renderStatus(item.status)}}</a></td>            
            <td><a href="javascript://"  @click="editItemForm(item.exp_number)">edit</a></td>
        </tr> 
    </tbody>           
    </table>

    </div> 
</template>
<style>
.btn{
    background: #4CAF50; /* Green */
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
    .items-list h1{
        text-align: center;
        background: dimgray;
        color: white;
    }
    #list-table{
        width:100%;
        
        margin:1vw;
        border-collapse: collapse;
        font-size: 150%;
    }
    #list-table tr{
        background: #f2f2f2;
        border-bottom: solid 1px #ddd;
    }
     #list-table th{
        text-align: left;
     }
    @media only screen and (max-width: 600px) {        
        #list-table{
            font-size: 100%;
            
        }
       
    }

</style>