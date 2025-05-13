const Helper = {

    renderTimeDate:(datetime:string) => {        
        let date = datetime.split(' ')[0].split('-').reverse().join('-');
        let time = datetime.split(' ')[1].substring(0, 5);
        return time + ' ' + date;
    },

    date: (datetime:string) => { 
        let date = new Date();
        if (typeof datetime!='undefined') {
            date = new Date(datetime);
        }
        let day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
        let month = (date.getMonth() + 1) < 10 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
        let year = date.getFullYear();
        return year + '-' + month + '-' + day;
    },

    time: (datetime:string) => { 
        let date = new Date();
        if (typeof datetime != 'undefined') {
            date = new Date(datetime);
        }
        let hours = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
        let minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
        return hours + ':' + minutes;
    }
    
}
export default Helper;