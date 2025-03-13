const Student_name = ["pat", "poch", "dar"];

function add(){
    const input = document.getElementById("myInput");
    const Add_array = input.value;
    Student_name.push(Add_array);
}

function remove(){
    const input = document.getElementById("myInput");
    const delete_array = input.value;
    for (let i = 0; i < cars.length; i++) {
        if(Student_name[i] != delete_array){
            
        }
        else{
            Studen_name.splice(i,i)
        }
    }
}

function refresh(){

}