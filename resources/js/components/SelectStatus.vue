<template>
    <select name="status" @change="change_value"  :value="selectedIndex" v-bind:class="cssClassToRender">        
        <option v-for="status in statuses" v-bind:key="statuses.indexOf(status)" :value="statuses.indexOf(status)">{{status}}</option>
    </select>
</template>
<script>
export default {
    name: 'SelectStatus',
    props: ['statuses','cssClass'],
    data() {
        return {
            selectedIndex: 0, // Default to the first index
            cssClassToRender:this.cssClass
        };
    },
    methods: {
        change_value(event) {
            this.selectedIndex = event.target.value; // Update the selected index based on the user's selection
            if (this.selectedIndex > 0) {
                this.cssClassToRender = this.cssClass + ' blue-select';
            } else { 
                this.cssClassToRender = this.cssClass
            }
            this.$emit('update:selectedStatus', this.selectedIndex); // Emit the selected status to the parent component
        }
    },
 
}
</script>
<style scoped>
select {    
    padding: 5px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 14px;   
}
.blue-select,select option{
    color:blue;
}
select option:first-child{
    color:gray;
}

@media only screen and (max-width: 600px) {  
    .search_select{
        width:60%;
    }
}
</style>