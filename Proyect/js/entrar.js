function ingresar() {
    const usuario = document.getElementById('txtUsuario').value;
    const contra = document.getElementById('txtPass').value;
    if (usuario == "" || contra == "") {
        alert("Llene los campos requeridos");
    } else if (usuario == "20030100" && contra == "123") {
        window.location = "eventos_Estudiantes.html";
    } else if (usuario == "20030100d" && contra == "123") {
        window.location = "eventos_Docentes.html";
    } else if (usuario == "20030100c" && contra == "123") {
        window.location = "coordinador.html";
    } else {
        alert("Datos incorrectos");
    }
}