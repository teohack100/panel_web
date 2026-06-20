<script>
function toggle_password(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("showhide2");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function new_password(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhide2");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function new_password2(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhide3");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function toggle_passwordz(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("showhidez2");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function new_passwordz(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhidez2");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function new_passwordz2(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhidez3");

    if (tag2.innerHTML == '<i class="fas fa-eye"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="fas fa-eye-slash"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

function downbold(){
    var a = document.body.appendChild(
        document.createElement("a")
    );
    a.download = "{$siteTitle}.html";
    a.href = "data:text/html," + document.getElementById("content").innerHTML;
    a.click();
}
</script>