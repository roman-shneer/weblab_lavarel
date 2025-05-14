<script lang="ts" setup>

</script>
<template>
    <span v-if="encryptionStatus==0"  class="encrypted_false">{{val}}</span>
    <span v-if="encryptionStatus==1" class="encrypted">--hidden--</span>
    <span v-if="encryptionStatus==2"  class="encrypted_true">{{value}}</span>
</template>
<script lang="ts">
import Enc from "../libraries/Enc";

export default {
    name: 'EncText',
    data() {        
        return {
            value: this.val,            
        }
    },
    props: [
        'val',
        'encrypt_pass',
        'encryptionStatus'
    ],
    methods: {
        
        tryDecrypt() {             
            if (this.encryptionStatus == 2) { 
                if (typeof this.encrypt_pass != 'undefined') {
                    this.value = Enc.decrypt(this.val, this.encrypt_pass);
                } else { 
                    //its fake
                    this.value = this.val;
                }
                
            }
        }

    },

    updated() {         
        this.tryDecrypt();
    },
    mounted() { 
        this.tryDecrypt();
    }  
}
</script>
<style scoped>
.encrypted{
    color:gray;
    filter: blur(3px);
}
.encrypted_false,.encrypted_true{
    font-size: 0.7em;
}
</style>