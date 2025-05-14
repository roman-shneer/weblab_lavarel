const Helper = {

    renderTimeDate:(datetime:string, condition:string | null = null) => {        
        let date = datetime.split(' ')[0].split('-').reverse().join('-');
        let time = datetime.split(' ')[1].substring(0, 5);
        if (condition == null) { 
            return time + ' ' + date;
        }
        if (condition == 'date') { 
            return date;
        }
        if (condition == 'time') { 
            return time;
        }
    },

    date: (datetime:string|null=null) => { 
        let date = new Date();
        if (datetime!=null) {
            date = new Date(datetime);
        }
        let day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
        let month = (date.getMonth() + 1) < 10 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
        let year = date.getFullYear();
        return year + '-' + month + '-' + day;
    },

    time: (datetime:string|null=null) => { 
        let date = new Date();
        if (datetime!=null) {
            date = new Date(datetime);
        }
        let hours = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
        let minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
        return hours + ':' + minutes;
    }
    
}
export default Helper;