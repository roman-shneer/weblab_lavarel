<script setup>
import { defineEmits } from "vue";
import SelectStatus from './SelectStatus.vue';
const $emit = defineEmits(["closeForm","renderTimeDate"]);
</script>
<template>
    <div id="edit-form">
        <button class="f-right material-symbols-outlined color-w" @click="$emit('closeForm')">close</button>
        <h5>Add/Edit Item</h5>      
        <table class="table-edit">
            <tbody>
                <tr>
                    <td>Exp. Id</td>
                    <td>
                        <input type="text" name="exp_number" @change="change_value" :value="state.exp_number" class="input" placeholder="Numbers,comma separated"/>
                        <div class="note">can be few, comma separated</div>
                    </td>
                </tr>
                <tr>
                    <td>Title</td>
                    <td>
                        <input type="text" name="title" @change="change_value" :value="state.title" class="input"/>
                        <div class="note">use short names</div>
                    </td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td>
                        <textarea name="description" @change="change_value" :value="state.description" class="input"></textarea>
                        <div class="note">Full name and processes</div>
                    </td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>
                        <input type="date"  name="date" @change="change_value" :value="state.date" />
                        <input type="time"  name="time" @change="change_value" :value="state.time" />
                    </td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>
                        <SelectStatus :statuses="statuses" v-model="state.status" @change="change_value" :value="state.status"></SelectStatus>
                    </td>
                </tr>
                <tr>
                    <td>Source</td>
                    <td><input type="text" name="source" @change="change_value"  :value="state.source" class="input"/></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button class="btn f-left" @click="save">Save</button>
                        <button class="btn bred f-right" @click="deleteTask" v-if="state.id>0">Delete</button>
                    </td>                    
                </tr>
            </tbody>
        </table>
        <ul class="subitems">
            <li v-for="(item, index) in subitems" :key="index" v-bind:class = "(state.id==item.id)?'active_subitem':''" @click="changeActiveSubitem(item.id)">
                <div>   
                    <span class="subitem-datetime nowrap">
                        {{Helper.renderTimeDate(item.datetime).split(' ')[0]}}                                              
                        <div><b>{{Helper.renderTimeDate(item.datetime).split(' ')[1]}}</b></div>
                    </span>                 
                    <span>
                        {{item.title}}                
                    </span>                      
                    <span>{{statuses[item.status]}}</span>               
                </div>                
                <div class="description">
                    {{item.description}}
                </div>
            </li>
            <li v-if="subitems.length==0">No subitems</li>
        </ul>
    </div>
    <div class="dark-layer"></div>
</template>

<script>

import axiosPro from '../libraries/axiosPro';
import Helper from '../libraries/helper';
export default {
    name: 'NewForm',
    props: ['editItem','statuses','subitems'],         
    state: {},     
    inheritAttrs: false,
   
    data() {
        const editItem = this.editItem;        
        let exp_number = editItem.exp_number;
        let title = editItem.title;
        let status = editItem.status;
        let source = editItem.source;

        if (this.subitems.length > 0) {
            exp_number = this.subitems[0].exp_number;
            title = this.subitems[0].title;
            status = this.subitems[0].status;
            source = this.subitems[0].source;
        }
        
        return {            
            'state': {
                id: 0,
                exp_number: exp_number,
                title: title,
                description: '',
                status: status,
                source: source,                            
                date:Helper.date(),
                time: Helper.time(),                
            },            
            encryptableVars:['title', 'description', 'source']
        }
    },
    methods: {        
        changeActiveSubitem(id) {             
            this.state = this.subitems.find((item) => item.id == id);
            this.state.date = this.state.datetime.split(' ')[0];
            this.state.time = this.state.datetime.split(' ')[1];            
        },
        deleteTask() { 
            axiosPro.delete('/task', {id:this.state.id}, () => {                                
                this.$emit('closeForm');
                this.$emit('search');
            }); 
        },
        change_value(e) {
            this.state[e.target.name] = e.target.value;                 
        },
        save() {       
            let args = this.state;
            args.datetime = args.date + ' ' + args.time;
            delete args.date;
            delete args.time;            
            this.$emit('saveForm', args);
        },
    },
    components: {
        SelectStatus,
    },
    

}
</script>
<style scoped>
.input{
    padding:1px 5px;
}
.color-w{
    color:#ffffff;
}
.subitems{
    white-space: inherit;
    margin:5px;
}
.subitems li{

    border-bottom:solid #ccc 1px;
}
.subitems li div span{
    display: inline-block;
    width: 45%;  
    vertical-align: top;
}
.subitems li div span:first-child{
    width: 30%;
}
.subitems li div span:last-child{
    width: 25%;
}
.subitem-datetime{
    font-size: 0.8em;
    line-height: 1;
}
.active_subitem{
    background: floralwhite;
}
#edit-form{
    border:solid black 1px;
    width:50vw;
    left:25vw;
    position:fixed;    
    top:5vh;
    z-index: 1;
    background: #fff;    
    height:90vh;
    overflow-y: auto;
}
#edit-form h5{
    background: dimgray;
    color:#FFF;
    text-align: center;
}
.table-edit input[type="text"],
.table-edit input[type="number"],
.table-edit input[type="date"],
.table-edit textarea,
.table-edit select{
    border:solid #ccc 1px;
    margin-bottom: 1vh;
}
.table-edit input[type="date"]{
    width: 40%;
}

.table-edit input[type="time"]{
    width: 40%;
}

.table-edit{
    margin:1vh;
    margin:0 auto;
}

.table-edit td{
    text-align: left;
    vertical-align: top;
}
.f-left{
    float:left;
}
.f-right{
    float:right;
}
.bred{
    background: red;
}
.nowrap{
    white-space: nowrap;
}
.description{
    white-space: pre-wrap;
    padding:5px 0px 5px 5px;
    border-top:dashed #ccc 1px;
    background: whitesmoke;
}
.dark-layer{
    background: gray;
    opacity: 0.7;
    filter: blur(10px);
    position: fixed;
    top:0;
    left:0;
    width:100vw;
    height:100vh;
}
.note{
    color:#000;
    font-size: 14px;
    line-height: 1;
    margin-bottom: 5px;
    font-style: italic;
}
@media only screen and (max-width: 600px) {     
    #edit-form{
        width:90vw;
        left:5vw;
    }
    #edit-form table{
        width:calc(100% - 2vh);
    }
    #edit-form table tr td:first-child{
        width:30%;
    }
    #edit-form table tr td:last-child input{
        width:90%;
    }
    #edit-form table tr td:last-child input[type="date"]{
        width:50%;
    }
    #edit-form table tr td:last-child input[type="time"]{
        width:32%;
        margin-left:10px;
    }
    #edit-form table tr td:last-child textarea{
        width:90%;
        height:10vh;
    }
}
</style>