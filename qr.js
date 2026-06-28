
  const contenedor=document.getElementById('qr');

    const hola=  new QRCode(contenedor, {
        text: `holaaaaa`,
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });
    
