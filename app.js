function mostrarSeccion(id) {
  // Oculta todas las secciones
  document.querySelectorAll('.section').forEach(sec => {
    sec.classList.remove('active');
    sec.style.display = 'none';
  });

  // Muestra la sección seleccionada
  const seccion = document.getElementById(id);
  if (seccion) {
    seccion.classList.add('active');
    seccion.style.display = 'block';
  }

  // Inicia o detiene el carrusel dependiendo de si estamos en "inicio"
  if (id === 'inicio') {
    startCarousel();
  } else {
    stopCarousel();
  }
}

// Mostrar solo "inicio" al cargar
document.addEventListener("DOMContentLoaded", () => {
  mostrarSeccion('inicio');
});


// =================== SECCIÓN: CARRUSEL CONTROL Y VISIBILIDAD ===================
let carouselInterval = null;
let currentIndex = 0;
let images, sectionInicio;

function showImage(index) {
  images.forEach(img => img.classList.remove('active'));
  images[index].classList.add('active');
}

function moveToNextSlide() {
  currentIndex = (currentIndex + 1) % images.length;
  showImage(currentIndex);
}

function moveToPrevSlide() {
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  showImage(currentIndex);
}

function startCarousel() {
  if (!carouselInterval) {
    carouselInterval = setInterval(moveToNextSlide, 3000);
  }
}

function stopCarousel() {
  clearInterval(carouselInterval);
  carouselInterval = null;
}

function checkVisibility() {
  const isVisible = sectionInicio.classList.contains('active');
  if (isVisible) {
    startCarousel();
  } else {
    stopCarousel();
  }
}
//
window.addEventListener("DOMContentLoaded", () => {
  sectionInicio = document.getElementById("inicio");
  images = document.querySelectorAll(".carousel-img");

  if (images.length > 0) {
    showImage(0);
    checkVisibility();

    document.querySelector('.carousel-btn.next').addEventListener('click', moveToNextSlide);
    document.querySelector('.carousel-btn.prev').addEventListener('click', moveToPrevSlide);
  }

  const deleteForm = document.getElementById("delete-form");
  const deleteInput = document.getElementById("delete-image-name");

  images.forEach((img, index) => {
    img.addEventListener("click", () => {
      // Deseleccionar si ya estaba seleccionada
      if (img.classList.contains("selected")) {
        img.classList.remove("selected");
        deleteForm.style.display = "none";
        deleteInput.value = "";
        startCarousel();
        return;
      }

      // Seleccionar nueva imagen
      images.forEach(i => i.classList.remove("selected"));
      img.classList.add("selected");
      deleteInput.value = img.dataset.filename;
      deleteForm.style.display = "block";
      stopCarousel();

      currentIndex = index;
      showImage(currentIndex);
    });
  });

  // ====== Manejo de navegación por menú lateral ======
  document.querySelectorAll(".menuLateral button").forEach(boton => {
    boton.addEventListener("click", () => {
      const id = boton.id.replace("btn-", "");
      mostrarSeccion(id);
    });
  });
});


// =================== CIERRE DE SESIÓN CON SWEETALERT ===================

document.addEventListener("DOMContentLoaded", function () {
  const cerrarSesionBtn = document.getElementById("cerrarSesion");
  if (cerrarSesionBtn) {
    cerrarSesionBtn.addEventListener("click", function () {
      Swal.fire({
        title: 'Sesión cerrada',
        text: 'Has cerrado sesión correctamente.',
        icon: 'success',
        confirmButtonText: 'Aceptar'
      }).then((result) => {
        if (result.isConfirmed) {
          localStorage.clear();
          sessionStorage.clear();
          window.location.href = "usuario.php";
        }
      });
    });
  }
});

/////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", () => {
  // Mostrar formulario de creación de producto
  document.getElementById("btn-crear")?.addEventListener("click", () => {
    document.getElementById("form-crear-producto").style.display = "block";
  });

  
  
window.previsualizarImagenProducto = function (event) {
  const input = event.target;
  const archivo = input.files[0];
  const vistaPrevia = document.getElementById("vista-previa-producto-creacion");

  if (archivo && vistaPrevia) {
    const lector = new FileReader();
    lector.onload = function (e) {
      vistaPrevia.src = e.target.result;
      vistaPrevia.style.display = "block";
    };
    lector.readAsDataURL(archivo);
  } else if (vistaPrevia) {
    vistaPrevia.src = "";
    vistaPrevia.style.display = "none";
  }
};

  // 👉 Función para cerrar formulario de creación
  window.cerrarFormularioCreacionProducto = function () {
    document.getElementById("form-crear-producto").style.display = "none";

    const formCrear = document.querySelector('#form-crear-producto form');
    if (formCrear) formCrear.reset();

    const vistaPrevia = document.getElementById("vista-previa-producto-creacion");
    if (vistaPrevia) {
      vistaPrevia.src = "";
      vistaPrevia.style.display = "none";
    }
  };

  // 👉 Función para cerrar formulario de edición
  window.cerrarFormularioEdicionProducto = function () {
    const formulario = document.getElementById("tabla-modificacion-producto");
    if (formulario) formulario.style.display = "none";

    const formEditar = document.getElementById("form-editar-producto");
    if (formEditar) formEditar.reset();

    const preview = document.getElementById("edit-imagen-preview");
    if (preview) {
      preview.src = "";
      preview.style.display = "none";
    }

    const nuevaVista = document.getElementById("vista-previa-producto-edicion");
    if (nuevaVista) {
      nuevaVista.src = "";
      nuevaVista.style.display = "none";
    }
  };

  // Mostrar formulario de edición de producto
  document.getElementById("boton-modificar")?.addEventListener("click", () => {
    const seleccionado = document.querySelector(".producto-card.seleccionado");
    if (seleccionado) {
      document.getElementById("edit-id").value = seleccionado.dataset.id;
      document.getElementById("edit-producto").value = seleccionado.dataset.producto;
      document.getElementById("edit-precio").value = seleccionado.dataset.precio;
      document.getElementById("edit-descripcion").value = seleccionado.dataset.descripcion;
      document.getElementById("imagen-actual").value = seleccionado.dataset.imagen;
      document.getElementById("edit-imagen-preview").src = "../" + seleccionado.dataset.imagen;
      document.getElementById("edit-imagen-preview").style.display = "block";
      document.getElementById("tabla-modificacion-producto").style.display = "block";
    } else {
    Swal.fire({
    icon: 'warning',
    title: '¡Atención!',
    text: 'Selecciona un producto primero.',
    confirmButtonText: 'Entendido'
    });
    }});

  // Eliminar producto
  document.getElementById("boton-eliminar")?.addEventListener("click", () => {
    const seleccionado = document.querySelector(".producto-card.seleccionado");
    
    if (seleccionado) {
      Swal.fire({
        title: "¿Eliminar Producto?",
        text: "¿Estas seguro que deseas eliminar este producto?.",
        icon: "warning",
        showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar"
      }).then(result => {
      if (result.isConfirmed){ 

      document.getElementById("eliminar-id").value = seleccionado.dataset.id;
      document.getElementById("etiqueta-eliminar").submit()};
    })}
    else{
      Swal.fire({
    icon: 'warning',
    title: '¡Atención!',
    text: 'Selecciona un producto primero.',
    confirmButtonText: 'Entendido'
    });
    }; 
  });

  // 👉 Seleccionar producto
  document.querySelectorAll('.producto-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.producto-card').forEach(c => c.classList.remove('seleccionado'));
      card.classList.add('seleccionado');

      window.productoSeleccionado = {
        id: card.dataset.id,
        producto: card.dataset.producto,
        precio: card.dataset.precio,
        descripcion: card.dataset.descripcion,
        imagen: card.dataset.imagen
      };
    });
  });

  // 👉 Vista previa de nueva imagen al editar
  const inputImagenEditar = document.getElementById("edit-imagen-producto");
  if (inputImagenEditar) {
    inputImagenEditar.addEventListener("change", function () {
      const archivo = this.files[0];
      const vistaPrevia = document.getElementById("vista-previa-producto-edicion");
      const imagenActual = document.getElementById("edit-imagen-preview");

      if (archivo) {
        const lector = new FileReader();
        lector.onload = function (e) {
          if (vistaPrevia) {
            vistaPrevia.src = e.target.result;
            vistaPrevia.style.display = "block";
          }
          if (imagenActual) {
            imagenActual.src = "";
            imagenActual.style.display = "none";
          }
        };
        lector.readAsDataURL(archivo);
      } else {
        if (vistaPrevia) {
          vistaPrevia.src = "";
          vistaPrevia.style.display = "none";
        }
      }
    });
  }
});


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", () => {
  // Mostrar formulario de creación
  document.getElementById("btn-crear-novedad")?.addEventListener("click", () => {
    document.getElementById("form-crear-novedades").style.display = "block";
  });

  
window.previsualizarImagenNovedad = function (event) {
  const input = event.target;
  const archivo = input.files[0];
  const vistaPrevia = document.getElementById("vista-previa-novedades");

  if (archivo && vistaPrevia) {
    const lector = new FileReader();
    lector.onload = function (e) {
      vistaPrevia.src = e.target.result;
      vistaPrevia.style.display = "block";
    };
    lector.readAsDataURL(archivo);
  } else if (vistaPrevia) {
    vistaPrevia.src = "";
    vistaPrevia.style.display = "none";
  }
};

  // 👉 Cerrar formulario de creación
  window.cerrarFormularioCreacion = function () {
    const contenedor = document.getElementById("form-crear-novedades");
    if (contenedor) contenedor.style.display = "none";

    const formulario = contenedor?.querySelector("form");
    if (formulario) formulario.reset();

    const vista = document.getElementById("vista-previa-novedades");
    if (vista) {
      vista.src = "";
      vista.style.display = "none";
    }
  };


  // 👉 Cerrar formulario de edición
  window.cerrarFormularioEdicion = function () {
    const formulario = document.getElementById("tabla-modificacion-novedad");
    if (formulario) formulario.style.display = "none";

    const form = document.getElementById("form-editar-novedad");
    if (form) form.reset();

    const preview = document.getElementById("vista-previa-novedad-edicion");
    if (preview) {
      preview.src = "";
      preview.style.display = "none";
    }

    const imagenActual = document.getElementById("edit-imagen-preview-novedad");
    if (imagenActual) {
      imagenActual.src = "";
      imagenActual.style.display = "none";
    }
  };

////////////////////
// Mostrar formulario de edición con datos
document.getElementById("boton-modificar-novedad")?.addEventListener("click", () => {
  const seleccionado = document.querySelector(".novedad-card.seleccionado");
  if (seleccionado) {
    document.getElementById("edit-id-novedad").value = seleccionado.dataset.id;
    document.getElementById("edit-nombre").value = seleccionado.dataset.nombre;
    document.getElementById("edit-descripcion-novedad").value = seleccionado.dataset.descripcion;

    const rutaImagen = seleccionado.querySelector("img")?.getAttribute("src");
    const imagenActual = document.getElementById("edit-imagen-preview-novedad");
    

    if (imagenActual && rutaImagen) {
      imagenActual.src = rutaImagen;
      imagenActual.style.display = "block";
    }

    document.getElementById("tabla-modificacion-novedad").style.display = "block";
  } else {
    Swal.fire({
    icon: 'warning',
    title: '¡Atención!',
    text: 'Selecciona una novedad primero.',
    confirmButtonText: 'Entendido'
    });
  }
});

  // 👉 Eliminar novedad
  document.getElementById("boton-eliminar-novedad")?.addEventListener("click", () => {
    const seleccionado = document.querySelector(".novedad-card.seleccionado");
    if (seleccionado) {
        Swal.fire({
        title: "¿Eliminar Novedad?",
        text: "¿Estas seguro que deseas eliminar esta novedad?.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      }).then(result => {
      if (result.isConfirmed){
      document.getElementById("eliminar-id-novedad").value = seleccionado.dataset.id;
      document.getElementById("form-eliminar-novedad").submit()};
    })} else {
      Swal.fire({
    icon: 'warning',
    title: '¡Atención!',
    text: 'Selecciona una novedad primero.',
    confirmButtonText: 'Entendido'
    });
    }
  });

  // 👉 Seleccionar tarjeta de novedad
  document.querySelectorAll(".novedad-card").forEach(card => {
    card.addEventListener("click", () => {
      document.querySelectorAll(".novedad-card").forEach(c => c.classList.remove("seleccionado"));
      card.classList.add("seleccionado");

      window.novedadSeleccionada = {
        id: card.dataset.id,
        nombre: card.dataset.nombre,
        descripcion: card.dataset.descripcion,
        imagen: card.querySelector("img")?.getAttribute("src")
      };
    });
  });

  
  // 👉 Vista previa nueva imagen al editar
  
  const inputImagenEditar = document.getElementById("edit-imagen-novedad");
  if (inputImagenEditar) {
    inputImagenEditar.addEventListener("change", function () {
      const archivo = this.files[0];
      const vistaPrevia = document.getElementById("vista-previa-novedad-edicion");
      const imagenActual = document.getElementById("edit-imagen-preview-novedad");
      

      if (archivo) {
        const lector = new FileReader();
        lector.onload = function (e) {
          if (vistaPrevia) {
            vistaPrevia.src = e.target.result;
            vistaPrevia.style.display = "block";
          }
          if (imagenActual) {
            imagenActual.src = "";
            imagenActual.style.display = "none";
          }
        };
        lector.readAsDataURL(archivo);
      } else {
        if (vistaPrevia) {
          vistaPrevia.src = "";
          vistaPrevia.style.display = "none";
        }
      }
    });
  }
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////HORARIOS///////////////////////////////////////////////////////////////////////
// apps.js
(function () {
  // Variables privadas del módulo
  let filaSeleccionadaManicure = null;
  let filaSeleccionadaCorte = null;

  document.addEventListener("DOMContentLoaded", () => {
    console.log("Modulo de horarios cargado");

    // Mostrar formulario Manicure
    document.getElementById('btn-nuevo-horario')?.addEventListener('click', function () {
      console.log("Botón manicure clickeado");
      const form = document.getElementById('formulario-nuevo-manicure');
      form.style.display = 'block';
      form.classList.remove('fade-out');
      form.classList.add('fade-in');
    });

    // Mostrar formulario Corte
    document.getElementById('btn-nuevo-horario-corte')?.addEventListener('click', function () {
      const form = document.getElementById('formulario-nuevo-corte');
      form.style.display = 'block';
      form.classList.remove('fade-out');
      form.classList.add('fade-in');
    });

    // Cancelar nuevo horario
    window.formularioCancelarNuevoHorario = function (tipo) {
      let form;

      if (tipo === 'manicure') {
        form = document.getElementById('formulario-nuevo-manicure');
      } else if (tipo === 'corte') {
        form = document.getElementById('formulario-nuevo-corte');
      }

      if (form) {
        form.classList.remove('fade-in');
        form.classList.add('fade-out');

        setTimeout(() => {
          form.style.display = 'none';
          form.classList.remove('fade-out');
          const innerForm = form.querySelector('form');
          if (innerForm) innerForm.reset();
        }, 300);
      }
    };

    // Selección de filas Manicure
    document.querySelectorAll('#horarios tbody tr').forEach(tr => {
      tr.addEventListener('click', function () {
        if (filaSeleccionadaManicure) {
          filaSeleccionadaManicure.classList.remove('seleccionado');
        }
        filaSeleccionadaManicure = this;
        this.classList.add('seleccionado');
      });
    });

    // Selección de filas Corte
    document.querySelectorAll('#horarios-corte tbody tr').forEach(tr => {
      tr.addEventListener('click', function () {
        if (filaSeleccionadaCorte) {
          filaSeleccionadaCorte.classList.remove('seleccionado');
        }
        filaSeleccionadaCorte = this;
        this.classList.add('seleccionado');
      });
    });

    // Eliminar Manicure
    document.getElementById('btn-eliminar')?.addEventListener('click', function () {
  if (!filaSeleccionadaManicure) {
    Swal.fire({
      icon: 'warning',
      title: '¡Atención!',
      text: 'Selecciona un horario primero.',
      confirmButtonText: 'Entendido'
    });
    return;
  }

  const celdas = filaSeleccionadaManicure.querySelectorAll('td');
  let tieneCitas = [...celdas].slice(1).some(celda => celda.textContent.trim().toLowerCase() === 'ocupado');

  if (tieneCitas) {
    Swal.fire({
      icon: 'warning',
      title: '¡Atención!',
      text: 'Este horario tiene citas agendadas, no se puede eliminar.',
      confirmButtonText: 'Entendido'
    });
    return;
  }

  Swal.fire({
    title: "¿Eliminar Horario?",
    text: "¿Estás seguro que deseas eliminar este horario?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar"
  }).then(result => {
    if (result.isConfirmed) {
      const hora = filaSeleccionadaManicure.dataset.id; 
      document.getElementById('input-id-horario').value = hora;
      document.getElementById('form-eliminar-horario').submit();
    }
  });
});




    // Eliminar Corte
    document.getElementById('btn-eliminar-corte')?.addEventListener('click', function () {
      if (!filaSeleccionadaCorte) {
        Swal.fire({
        icon: 'warning',
        title: '¡Atención!',
        text: 'Selecciona un horario primero.',
        confirmButtonText: 'Entendido'
    });
        return;
      }

      const celdas = filaSeleccionadaCorte.querySelectorAll('td');
      let tieneCitas = [...celdas].slice(1).some(celda => celda.textContent.trim().toLowerCase() === 'ocupado');

      if (tieneCitas) {
        Swal.fire({
        icon: 'warning',
        title: '¡Atención!',
        text: 'Este horario tiene citas agendadas, no se puede eliminar.',
        confirmButtonText: 'Entendido'
    });
        return;
      }
      Swal.fire({
      title: "¿Eliminar Horario?",
      text: "¿Estás seguro que deseas eliminar este horario?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar"
  }).then(result=>{
    if (result.isConfirmed){
      const hora = filaSeleccionadaCorte.dataset.id;
      document.getElementById('input-id-horario-corte').value = hora;
      document.getElementById('form-eliminar-horario-corte').submit();
    }
  })
      
    });

    // Doble click editar Manicure
    document.querySelectorAll('#horarios tbody tr').forEach(tr => {
      tr.addEventListener('dblclick', function () {
        const celdas = this.querySelectorAll('td');
        const hora = celdas[0].textContent.trim();
        const id = this.dataset.id;

        document.getElementById('editar-id-horario').value = id;
        document.getElementById('editar-hora').value = hora;

        const dias = ["lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];
        dias.forEach((dia, i) => {
          const valor = celdas[i + 1].textContent.trim().toLowerCase();
          const checkbox = document.getElementById('editar-' + dia);
          checkbox.checked = (valor === 'disponible');
          checkbox.disabled = (valor === 'ocupado');
        });

        const form = document.getElementById('formulario-editar-horario');
        form.style.display = 'block';
        form.classList.remove('fade-out');
        form.classList.add('fade-in');
      });
    });

    window.formularioCancelarEditarHorario = function () {
      const form = document.getElementById('formulario-editar-horario');
      form.classList.remove('fade-in');
      form.classList.add('fade-out');

      setTimeout(() => {
        form.style.display = 'none';
        form.classList.remove('fade-out');
        form.querySelector('form').reset();
      }, 300);
    };

    // Doble click editar Corte
    document.querySelectorAll('#horarios-corte tbody tr').forEach(tr => {
      tr.addEventListener('dblclick', function () {
        const celdas = this.querySelectorAll('td');
        const hora = celdas[0].textContent.trim();
        const id = this.dataset.id;

        document.getElementById('editar-id-horario-corte').value = id;
        document.getElementById('editar-hora-corte').value = hora;

        const dias = ["lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];
        dias.forEach((dia, i) => {
          const valor = celdas[i + 1].textContent.trim().toLowerCase();
          const checkbox = document.getElementById('editar-corte-' + dia);
          checkbox.checked = (valor === 'disponible');
          checkbox.disabled = (valor === 'ocupado');
        });

        const form = document.getElementById('formulario-editar-horario-corte');
        form.style.display = 'block';
        form.classList.remove('fade-out');
        form.classList.add('fade-in');
      });
    });

    window.formularioCancelarEditarHorarioCorte = function () {
      const form = document.getElementById('formulario-editar-horario-corte');
      form.classList.remove('fade-in');
      form.classList.add('fade-out');

      setTimeout(() => {
        form.style.display = 'none';
        form.classList.remove('fade-out');
        form.querySelector('form').reset();
      }, 300);
    };

  }); // Fin DOMContentLoaded
})();
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////citas///////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", () => {
  const botonAbrirFormularioCita = document.getElementById("btn-crear-cita");
  const contenedorFormularioCita = document.getElementById("form-crear-citas");
  const selectTipoServicio = document.getElementById("tipo_servicio");
  const divHorariosManicure = document.getElementById("horarios_manicure_div");
  const divHorariosCorte = document.getElementById("horarios_corte_div");
  const inputHoraOculta = document.getElementById("hora");
  const inputDiaOculta = document.getElementById("dia");

  function mostrarHorariosCita() {
    if (!selectTipoServicio || !divHorariosManicure || !divHorariosCorte) return;

    const tipo = selectTipoServicio.value;

    divHorariosManicure.style.display = tipo === "horarios_manicure" ? "block" : "none";
    divHorariosCorte.style.display = tipo === "horarios_corte" ? "block" : "none";
  }

  function extraerHoraDesdeSelectCita(selectElement) {
    if (!selectElement || !selectElement.value.includes("|")) return;

    const [dia, hora] = selectElement.value.split("|");
    inputDiaOculta.value = dia;
    inputHoraOculta.value = hora;
  }

  function cerrarFormularioCita() {
    if (!contenedorFormularioCita) return;

    contenedorFormularioCita.classList.remove("fade-in");
    contenedorFormularioCita.classList.add("fade-out");

    setTimeout(() => {
      contenedorFormularioCita.style.display = "none";
      contenedorFormularioCita.classList.remove("fade-out");

      const form = contenedorFormularioCita.querySelector("form");
      if (form) form.reset();

      inputDiaOculta.value = "";
      inputHoraOculta.value = "";
    }, 300);
  }

  // Abrir formulario y setear inputs ocultos al abrir
  if (botonAbrirFormularioCita && contenedorFormularioCita) {
    botonAbrirFormularioCita.addEventListener("click", function () {
      contenedorFormularioCita.style.display = "block";
      contenedorFormularioCita.classList.remove("fade-out");
      contenedorFormularioCita.classList.add("fade-in");
      mostrarHorariosCita();

      // Actualizar inputs ocultos con el valor del select visible
      let selectVisible = null;
      if (selectTipoServicio.value === "horarios_manicure") {
        selectVisible = divHorariosManicure.querySelector("select");
      } else {
        selectVisible = divHorariosCorte.querySelector("select");
      }
      if (selectVisible) {
        extraerHoraDesdeSelectCita(selectVisible);
      }
    });
  }

  // Cuando cambia el tipo de servicio, actualizar horarios y inputs ocultos
  if (selectTipoServicio) {
    selectTipoServicio.addEventListener("change", () => {
      mostrarHorariosCita();

      // Actualizar inputs ocultos con nuevo select visible
      let selectVisible = null;
      if (selectTipoServicio.value === "horarios_manicure") {
        selectVisible = divHorariosManicure.querySelector("select");
      } else {
        selectVisible = divHorariosCorte.querySelector("select");
      }
      if (selectVisible) {
        extraerHoraDesdeSelectCita(selectVisible);
      }
    });
  }

  // Aquí, agrega eventos onchange a ambos selects de horarios para actualizar inputs ocultos
  if (divHorariosManicure) {
    const selectManicure = divHorariosManicure.querySelector("select");
    if (selectManicure) {
      selectManicure.addEventListener("change", () => {
        extraerHoraDesdeSelectCita(selectManicure);
      });
    }
  }
  if (divHorariosCorte) {
    const selectCorte = divHorariosCorte.querySelector("select");
    if (selectCorte) {
      selectCorte.addEventListener("change", () => {
        extraerHoraDesdeSelectCita(selectCorte);
      });
    }
  }

  // Exponer funciones globalmente para el HTML si las usas ahí
  window.extraerHora = extraerHoraDesdeSelectCita;
  window.mostrarHorarios = mostrarHorariosCita;
  window.cerrarFormularioCreacionCitas = cerrarFormularioCita;

  // Mostrar horarios inicialmente
  mostrarHorariosCita();
});

document.addEventListener("DOMContentLoaded", () => {
  let filaSeleccionadaUsuario = null;

  document.querySelectorAll("#usuarios table tbody tr").forEach(fila => {
    fila.addEventListener("click", () => {
      document.querySelectorAll("#usuarios table tbody tr").forEach(f => f.classList.remove("seleccionada"));
      fila.classList.add("seleccionada");
      filaSeleccionadaUsuario = fila;
    });
  });




  //citas
 



 const botonCrear = document.getElementById("btn-crear-usuario");
if (botonCrear) {
  botonCrear.addEventListener("click", () => {
    const tabla = document.getElementById("tabla-nuevo-usuario");
    if (tabla) {
      tabla.style.display = "block";
    }
  });
}

//editar usuario
 document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "btn-modificar-usuario") {
    if (!filaSeleccionadaUsuario) {
      Swal.fire("Usuario no seleccionado", "Selecciona un usuario primero", "warning");
      return;
    }

    const datos = filaSeleccionadaUsuario.dataset;

    document.getElementById("editar-id").value = datos.id;
    document.getElementById("editar-nombre").value = datos.nombre;
    document.getElementById("editar-telefono").value = datos.telefono;
    document.getElementById("editar-tipo").value = datos.tipo;

    document.getElementById("tabla-editar-usuario").style.display = "block";
  }
});




  // Eliminar usuario
  document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "btn-eliminar-usuario") {
    if (!filaSeleccionadaUsuario) {
      Swal.fire("Usuario no seleccionado", "Selecciona un usuario primero", "warning");
      return;
    }

    const datos = filaSeleccionadaUsuario.dataset;

    Swal.fire({
      title: "¿Eliminar usuario?",
      text: `¿Estás seguro de eliminar a ${datos.nombre}?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar"
    }).then(result => {
      if (result.isConfirmed) {
        document.getElementById("eliminar-id-usuario").value = datos.id;
        document.getElementById("form-eliminar-usuario").submit();
      }
    });
  }
});

});


// Cancelar formularios
function formularioCancelarNuevoUsuario() {
  document.getElementById("tabla-nuevo-usuario").style.display = "none";
}

function formularioCancelarEditarUsuario() {
  document.getElementById("tabla-editar-usuario").style.display = "none";
  document.querySelectorAll("#usuarios table tbody tr").forEach(f => f.classList.remove("seleccionada"));
}
//
//
//
      document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("busquedaProductos");

  if (input) {
    input.addEventListener("input", function () {
      const filtro = this.value.toLowerCase().trim();
      const productos = document.querySelectorAll(".producto-card");

      productos.forEach(function (producto) {
        const nombre = producto.getAttribute("data-producto").toLowerCase().trim();
        if (nombre.includes(filtro) || filtro === "") {
          producto.style.display = "";
        } else {
          producto.style.display = "none";
        }
      });
    });
  }
});


      document.getElementById("busquedaNovedades").addEventListener("input", function() {
        var filtro = this.value.toLowerCase().trim();
        var novedades = document.querySelectorAll(".novedad-card");

        novedades.forEach(function(novedad) {
          var nombre = novedad.getAttribute("data-nombre").toLowerCase().trim();
          if (nombre.includes(filtro) || filtro === "") {
            novedad.style.display = "";
          } else {
            novedad.style.display = "none";
          }
        });
      });
    const inputBusqueda = document.getElementById('busquedaCitas');
if (inputBusqueda) {
  inputBusqueda.addEventListener('input', function () {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaCitas tbody tr');

    filas.forEach(fila => {
      const textoFila = fila.textContent.toLowerCase();
      fila.style.display = textoFila.includes(filtro) ? '' : 'none';
    });
  });
}



      const input = document.getElementById("busquedaUsuarios");
if (input) {
  input.addEventListener("input", function() {
    var filtro = this.value.toLowerCase().trim();
    var filas = document.querySelectorAll("#tablaUsuarios tbody tr");

    filas.forEach(function(fila) {
      let contenido = Array.from(fila.cells).map(td => td.textContent.toLowerCase()).join(" ");
      fila.style.display = contenido.includes(filtro) ? "" : "none";
    });
  });
}

