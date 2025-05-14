<script lang="ts" setup>
import axiosPro from '../libraries/axiosPro';
</script>
<template>
    <br/><br/><br/>
    <ul class="navbar-nav ms-auto">
        <!-- Authentication Links -->     
        <li class="nav-item">
            <a class="dropdown-item" href="/setting">
                <span v-if="encryptionStatus==0">Encrypt Data</span>
                <span v-if="encryptionStatus>0">Decrypt Data</span>
            </a>            
        </li>   
        <li class="nav-item">
            <a class="dropdown-item" @click="axiosPro.logout">Logout</a>            
        </li>                   
    </ul>
</template>
<script lang="ts">
export default {
    props:['encryptionStatus'],
    data() {
        
       
    },
    methods: {
       
    },
   
}
</script>
<style scoped>
.navbar-nav{
    position:fixed;
    top:24px;
    right:20vw;
    padding:1em;
    box-shadow: 1px 1px 5px gray;
    background: white;
    z-index: 1;
}
@media only screen and (max-width: 600px) {
    .navbar-nav{
        right:0;
    }
}
</style>