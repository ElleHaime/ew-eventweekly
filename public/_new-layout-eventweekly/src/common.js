$(function() {
    
var dodchuk = {
    firstname: "max",
    lastname: "dodchuk",
    position: "front-end developer",

    sayMyName: function() {
        return this.firstname + " " + this.lastname;
    }
}

console.log(dodchuk.sayMyName());


});