<script lang="ts" setup>
    import Header from '../components/Header.vue';
    import axiosPro from '../libraries/axiosPro';
    import Enc from "../libraries/Enc";
</script>
<template>
    <Header :encrypted="encryptedStat"></Header>    
    <div class="main-div">                
        <div class="m-bottom-1">
            <label v-if="!encryptedStat" class="center">Encrypt your data</label>                       
            <label v-if="encryptedStat" class="center">Your data encrypted!</label>
        </div>
        <div class="m-bottom-1">
            <input type="password" placeholder="your own key" class="input-key" @change="setKey" autocomplete="new-password"/>
            <div>
                <small class="red">Remember the key, this action impossible to revert!</small>
            </div>
        </div>
        <div class="m-bottom-1">
            <button  v-if="!encryptedStat" class="btn-encrypt" @click="encrypt">Encrypt</button>
            <button  v-if="encryptedStat" class="btn-encrypt" @click="decrypt">Decrypt</button>
        </div>
    </div>
</template>
<script lang="ts">
export default { 
        props:['encrypted'],
        data() { 
            
            return {
                'key': '',
                'encryptedStat': this.encrypted,                
            };
        
        },
        methods: { 
            setKey(evt:any) { 
                this.key = evt.target.value;                              
            },
            encrypt() { 
                
                axiosPro.post("/encrypt_data", {
                    key: this.key,
                    crypted_example:Enc.encrypt('test',this.key)
                }, () => {                   
                    this.encryptedStat = true;
                });
            },
            decrypt() { 
                axiosPro.post("/decrypt_data", {
                    key: this.key,
                    crypted_example:'test'
                }, () => {                    
                    this.encryptedStat = false;
                });
            }
        },      
        components: {            
            Header,               
        },

    }
</script>
<style scoped>
    header{
        text-align: center;
        background: dimgray;
        color: white;
        position:fixed;
        width:100%;    
        top:0;    
        z-index: 1;
    }
    .main-div{        
        position:absolute;
        padding:10px;
        width:400px;        
        margin: 30px auto 0 auto;
        top:0;
        left:calc(50% - 200px);
    }
    .input-key{
        border:solid black 1px;
        width:100%;
    }
    .btn-encrypt{
        background: dimgray;
        color:white;
        width:80%;
        margin-left:10%;        
    }
    .m-bottom-1{
        margin-bottom:1em;
    }
    .red{
        color:red;
    }
    .center{
        display:block;
        text-align: center;
    }
    @media only screen and (max-width: 600px) {
        .main-div{
            margin-top:30px 0 0 0;
            width:100%;
            padding:10px;
            position: absolute;
            top:0;
        }
    }
</style>