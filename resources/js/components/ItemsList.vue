<template>
  <div class="items-list">
  
    <h1>Web Lab</h1>
    <StatusForm :statuses="statuses"/>
    <div>
        <span class="search-cell">
            <label>Tara Id:</label>
            <input type="text" v-model="selectedId"/>
            <button class="search-x" @click="selectedId = '';">x</button>
        </span>
        <span class="search-cell">
            <label>Title:</label>
            <input type="text" name="name" v-model="selectedName"/>
            <button class="search-x" @click="selectedName = '';">x</button>
        </span>
        <span class="search-cell">
            <label>Date:</label> 
            <input type="text" name="date" v-model="selectedDate"/>
            <button class="search-x" @click="selectedDate = '';">x</button>
        </span>
        <span class="search-cell">
            <label>Status:</label> 
            <input type="text" name="status" v-model="selectedStatus"/>
            <button class="search-x" @click="selectedStatus = '';">x</button>
        </span>
        <span class="search-cell">
            <label>Source:</label>
            <input type="text" name="source" v-model="selectedSource"/>
            <button class="search-x" @click="selectedSource = '';">x</button>
        </span>
        <span class="search-cell">
            <button @click="search" class="search-btn">Search</button>
        </span>
    </div>
    <NewForm :editItem="editItem" :statuses="statuses" v-if="showNewForm"  @closeForm="closeForm" @setItems="setItems" @setMax="setMax"/>
    <br/>
    <div>{{message}}</div>
    <button @click="addNewForm">Add records</button>
    <table border="1" id="list-table">
       <tbody>
         <tr>
            <th>Tara Id</th>
            <th>Title</th>
            <th>Date</th>
            <th>Status</th>
           
            <th>Next Id:{{max_id}}</th>
        </tr>
      
        <tr v-for="item in items" v-bind:key="item.id">
            <td @click="setId"><a href="javascript://"  @click="setId" title="{{item.description}}">{{item.exp_number}}</a></td>
            <td><a href="javascript://"  @click="setName">{{item.title}}</a></td>            
            <td><a href="javascript://"  @click="setDate">{{item.date}}</a></td>
            <td><a href="javascript://"  @click="setStatus">{{renderStatus(item.status)}}</a></td>
           
            <td><a href="javascript://"  @click="addEditForm(item.id)">edit</a></td>
        </tr> 
       </tbody>           
       
    </table>
  </div>
</template>
<script setup>
import NewForm from './NewForm.vue';
import StatusForm from './StatusForm.vue';

</script>
<script>

import HelpProvider from './HelpProvider';

export default {
    name:'ItemsList',
    data() {
        return {
            max_id: 0,
            items: [],
            statuses:[],
            selectedId: '',
            selectedName: '',
            selectedDate: '',
            selectedStatus: '',
            selectedSource: '',
            message: '',
            showNewForm: false,
            showStatusForm:false
        }
    },
    methods: {
        renderStatus(status) { 
            
            if (typeof this.local_statuses[status] != 'undefined') {
                return this.local_statuses[status];
            } else { 
                return "unknow "+status;
            }
        },

        setItems(items) { 
            this.items = items;        
        },

        setMax(maxnum) { 
            this.max_id=maxnum         
        },

        closeForm() { 
            this.editItem = {};
            this.showNewForm = false;
        },

        addEditForm(id) {
            
            this.editItem = this.items.filter(v => v.id == id)[0];            
            this.showNewForm = true
        },

        addNewForm() { 
            const d = new Date();
            const date = `${d.getFullYear()}-${("0"+(d.getMonth()+1)).slice(-2)}-${("0" + d.getDate()).slice(-2)}`;
            
            this.editItem = {
                exp_number: '',
                title: '',
                description: '',
                date: date,
                source: 0,
                status:0
            }
            this.showNewForm = true           
        },

        setDate(e) { 
             this.selectedDate=e.target.innerText
        },

        setStatus(e) { 
             this.selectedStatus=e.target.innerText
        },

        setId(e) {             
            this.selectedId=e.target.innerText
        },

        setName(e) { 
            this.selectedName=e.target.innerText
        },

        search() { 
            if (this.selectedId == '' && this.selectedName == '' && this.selectedDate == '' && this.selectedStatus == '' && this.selectedSource == '') {
                this.message = "Nothing to search";
            } else { 
                 this.message = "searching..."
            }
            const params = {
                exp_number: this.selectedId,
                title: this.selectedName,
                date: this.selectedDate,
                status:this.selectedStatus
            }
            HelpProvider.send('get','tasks',params,(response) => {                       
                this.max_id = response.data.max_id.toString();                    
                this.message = "found " + response.data.items.length + " items";                                          
                this.items = response.data.items;             
            });

            
        },

        load_statuses() { 
            HelpProvider.send('get', 'statuses', {}, (response) => { 
                this.statuses = response.data.statuses;                
                this.local_statuses = {};
                this.statuses.forEach(status => {
                    this.local_statuses[status.id] = status.name;
                });    
            });
        },

        components: {
            NewForm,
            StatusForm,
        }
    },

    mounted() {
        this.load_statuses()  
        this.search()
    },
  
}

</script>
<style scoped>
    #list-table{
        width:100%;
        margin:0 auto;
        border-collapse: collapse;
        font-size: 150%;
    }
    .search-cell{
        margin-right: 2%;
    }
    @media only screen and (max-width: 600px) {        
        #list-table{
            font-size: 100%;
            
        }
        .search-cell{
            display: block;
            text-align: left;   
            margin-right: 0;
            margin-bottom:5px;
        }
        .search-cell label{
            width:20%;
            margin-left:5%;
            display: inline-block;
        }
        .search-cell input{
            width:60%;
        }
        .search-cell .search-btn{
            width:90%;
            margin:0 5%;
        }
        .search-cell .search-x{
            padding:0 5px;
            background: transparent;
            border:0
        }
    }

</style>