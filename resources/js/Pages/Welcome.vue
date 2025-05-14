<template>
    <div class="header">        
        <a href="/login">Login</a>            
        <a href="/register">SignUp</a>        
    </div>
    <h1>Web Lab</h1>
    <div class="definition">
        <p>Open source system for manage Biology Laboratory experiments</p>
        <p>Support full data encryption</p>
        <p><a href="/register" class="reg_link">Free registration</a></p>
    </div>
</template>
<style scoped>
    .header{
        text-align: right;
        width:800px;
        margin:0 auto;
    }
    .header a{
        padding:1em;
        color: blue;
        text-decoration: underline;
    }
    h1{
        text-align: center;
        font-size: 150%;
    }
    .definition{
        color:navy;
        padding:2em;
        width:800px;
        margin:0 auto;
        border:dotted navy 1px;
    }
    header{
        text-align: right;
        margin:0 auto;
        width:800px
    }
    nav a{
        padding:1em;
    }
    .reg_link{
        color:blue;
        text-decoration: underline;
    }
    @media only screen and (max-width: 600px) {
        .header{
            width:100%;
        }
        .definition{        
            width:90%;        
           
        }
    }    
</style>