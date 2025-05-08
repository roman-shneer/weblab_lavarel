<template>
    <div>
        <span class="search-cell">
            <label>Exp. Id:</label>
            <input type="text" name="exp_number" v-model="searchFormAttributes.exp_number"/>
            <button class="search-x" @click="searchFormAttributes.exp_number = '';">x</button>
        </span>
        <span class="search-cell">
            <label>Title:</label>
            <input type="text" name="title" v-model="searchFormAttributes.title"/>
            <button class="search-x" @click="searchFormAttributes.title = '';">x</button>
        </span>
        <span class="search-cell">
            <label>Date:</label> 
            <input type="date" name="date" v-model="searchFormAttributes.date"/>
            <button class="search-x" @click="searchFormAttributes.date = '';">x</button>
        </span>
        <span class="search-cell">
            <label>Status:</label> 
            <SelectStatus :statuses="statuses" :value="searchFormAttributes.status" v-model="searchFormAttributes.status" @change="changeStatus"/> 
            <button class="search-x" @click="searchFormAttributes.status = 0;">x</button>          
        </span>
        <span class="search-cell">
            <label>Source:</label>
            <input type="text" name="source" v-model="searchFormAttributes.source"/>
            <button class="search-x" @click="searchFormAttributes.source = '';">x</button>
        </span>
        <span class="search-cell">
            <button @click="search" class="btn search-btn">Search</button>
        </span>
    </div>
</template>
<script lang="ts">
import SelectStatus from '../components/SelectStatus.vue';
export default {
    name: 'SearchForm',
    props: {
        searchFormAttributes: {
            type: Object,
            required: true,
            default: () => ({ exp_number: '', title: '', date: '', status: 0, source: '' })
        },
        'statuses': {
            type: Array,
            required: true,
            default: () => (['', 'In progress', 'Completed', 'Cancelled'])
        }
    },
    data() {
        
        return {                                                                 
        };
    },
    methods: {
        changeStatus(e: any) {         
            console.log('changeStatus', e.target.value);
            this.searchFormAttributes.status = e.target.value;            
        },      
        search() {             
            this.$emit('search');            
        },

    },
    components: {
        SelectStatus
    },
   
}
</script>
<style scoped>
.search-cell{
    margin-right: 2%;
}
@media only screen and (max-width: 600px) {        

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
            background: floralwhite;           
            color:dimgray
        }
        .search-cell .search-x{
            padding:0 5px;
            background: transparent;
            border:0
        }
    }
</style>