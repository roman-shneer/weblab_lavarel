<script  lang="ts" setup>
import { defineEmits } from "vue";
import Navigator from '../components/Navigator.vue';
const $emit = defineEmits(["setEncryptPass"]);

const encryptIconClass = "material-symbols-outlined encrypted";
const dataDecryptedOnClass = "green";
const dataDecryptedOffClass = "red";
</script>
<template>    
    <header>
        <div class="header-container">
            <span  v-bind:class="[encryptIconClass, (encryptionStatus==2) ? dataDecryptedOnClass : dataDecryptedOffClass]" v-if="encryptionStatus>0" @click="showPassForm=!showPassForm">encrypted</span>
            <span class="material-symbols-outlined encrypted" v-if="encryptionStatus==0" @click="showPassForm=!showPassForm">encrypted_off</span>
            <h1><a href="/home" >Web Lab</a></h1>  
            <span class="material-symbols-outlined setting_link" @click="showNavigator=!showNavigator">settings</span>
            <div v-if="showPassForm||encryptionStatus==1">
                <div v-if="encryptionStatus==1">
                    Your data encrypted&nbsp;<br/>
                    Set password:
                    <input type="password" class="password" placeholder="your encrypt password" v-model="password" @change="setEncryptPass"/>
                    <button class="material-symbols-outlined color-w" @click="dropPass">close</button>
                    <button @click="decrypt" class="decrypt-btn">Unlock</button>
                </div>
                <div v-if="encryptionStatus==2">
                    Your data already decrypted <button  @click="dropPass" class="reg_link">Lock</button>
                </div>
                <div v-if="encryptionStatus==0" class="left">Your data not encrypted <a href="/setting" class="reg_link">Encrypt data</a></div>
            </div>
        </div>        
    </header>
    <Navigator :encryptionStatus="encryptionStatus" v-if="showNavigator"></Navigator>
</template>
<script lang="ts">

export default {
    props: ['encrypt_pass','encryptionStatus'],
    data() {
        return {
            'showPassForm': false,
            'showNavigator': false,
            'password':this.encrypt_pass
        }
    },
    methods: {
        setEncryptPass(evt:any) { 
            this.password = evt.target.value;            
        },
        decrypt() { 
            this.$emit('setEncryptPass', this.password);
        },
        dropPass() { 
            this.password='';
            
        }
    },
    components:{ 
        Navigator
    }
};
</script>
<style scoped>
.setting_link{
    float:right;
}
header{
    text-align: center;
    background: dimgray;
    color: white;
    position:fixed;
    width:100%;
    top:0;    
    z-index: 1;
}
header .encrypted{
    float:left;
}
.header-container{
    width:60vw;
    margin: 0 auto;
}
header h1{
    display: inline-block;
}
.left{
    text-align: left;
}
.green{
    color:lawngreen
}
.red{
    color:red;
}
.password{
    background:white ;
    color:black;
    width:30%;
    margin-bottom:5px;
}
.reg_link{
    color:lightblue;
    text-decoration: underline;
}
.decrypt-btn{
    background: green;
    color:white;
    padding:1px 4px;
    border-radius: 3px;
    margin-left:20px;
}

@media only screen and (max-width: 600px) {   
    .header-container{
        width:100vw;
    }
}
</style>