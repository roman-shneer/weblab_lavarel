<template>
    <select name="status" @change="change_value"  :value="selectedIndex" :style="css">        
        <option v-for="status in statuses" v-bind:key="statuses.indexOf(status)" :value="statuses.indexOf(status)">{{status}}</option>
    </select>
</template>
<script>
export default {
    name: 'SelectStatus',
    props: ['statuses','css'],
    data() {
        return {
            selectedIndex: 0, // Default to the first index
        };
    },
    methods: {
        change_value(event) {
            this.selectedIndex = event.target.value; // Update the selected index based on the user's selection
         
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
</style>