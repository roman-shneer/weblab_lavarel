<script  lang="ts" setup>
import { defineEmits } from "vue";
const $emit = defineEmits(["setEncryptPass"]);

const encryptIconClass = "material-symbols-outlined encrypted";
const dataDecryptedOnClass = "green";
const dataDecryptedOffClass = "red";
</script>
<template>
    <header>
        <span  v-bind:class="[encryptIconClass, (encryptionStatus==2) ? dataDecryptedOnClass : dataDecryptedOffClass]" v-if="encryptionStatus>0" @click="showPassForm=!showPassForm">encrypted</span>
        <span class="material-symbols-outlined encrypted" v-if="encryptionStatus==0" @click="showPassForm=!showPassForm">encrypted_off</span>
        <h1><a href="/" >Web Lab</a></h1>  
        <a href="/home" class="setting_link"><span class="material-symbols-outlined">settings</span></a>
        <div v-if="showPassForm">
            <div v-if="encryptionStatus>0">Your data encrypted&nbsp;
                <input type="password" class="password" placeholder="your encrypt password" v-model="password" @change="setEncryptPass"/>
                <button class="material-symbols-outlined color-w" @click="dropPass">close</button>
            </div>
            <div v-if="encryptionStatus==0" class="left">Your data not encrypted</div>
        </div>
                  
    </header>
</template>
<script lang="ts">

export default {
    props: ['encrypt_pass','encryptionStatus'],
    data() {
      
        return {
            'showPassForm': false,
            'password':this.encrypt_pass
        }
    },
    methods: {
        setEncryptPass(evt:any) { 
            console.log('header setEncryptPass',evt.target.value);
            this.$emit('setEncryptPass', evt.target.value);
        },
        dropPass() { 
            this.password='';
            this.$emit('setEncryptPass', '');
        }
    }
};
</script>
<style scoped>
.setting_link{
    position: absolute;
    right:1px;
}
header{
        text-align: center;
        background: dimgray;
        color: white;
        position:fixed;
        width:100%;    
        top:0;    
}
header .encrypted{
    float:left;
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
    background:dimgray ;
    width:30%;
}
</style>