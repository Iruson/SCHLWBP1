<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$creditos = $_SESSION['creditos'];
// 1. DEFINIR EL UMBRAL
const UMBRAL_BAJO_CREDITOS = 60; 

// 2. ESTABLECER LA BANDERA
$mostrar_aviso = false; 

// 3. CHEQUEO Y LÓGICA DE CRÉDITOS
if (isset($_SESSION['creditos']) && is_numeric($_SESSION['creditos'])) {
    
    $creditos_actuales = (int)$_SESSION['creditos'];

    // Si los créditos son menores al umbral (60), activamos el aviso.
    if ($creditos_actuales < UMBRAL_BAJO_CREDITOS) {
        $mostrar_aviso = true;
    }
} 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LCDA - Página principal</title>
    <link rel="stylesheet" href="css2/main_clean.css">
    <link rel="stylesheet" href="css2/slider.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>

<div id="avisoCreditos" class="aviso-flotante">
    <p>⚠️ <strong>Aviso Importante:</strong> ¡Atención padre/representante! Sus créditos están bajos. Por favor, asegúrese de realizar sus <strong>pagos a tiempo</strong> para mantener el acceso completo a la pestaña de <strong>Descargas</strong>.</p>
    <button id="cerrarAviso" class="btn-cerrar" disabled>Cerrar (Esperando 10s)</button>
</div>


<header class="header">
    <p class="welcome">Bienvenido, <?php echo $_SESSION['nombre_usuario']; ?></p>

    <div class="topbar">
        <div class="logo">
            <img src="miscelaneos/logo_header.jpg" alt="Logo del colegio">
            <h1>U.E.P. <span>LUISA CÁCERES DE ARISMENDI</span></h1>
        </div>

        <nav class="navbar">
            <ul>
                <li><a href="index.php" class="active">Inicio</a></li>
                <li><a href="about.php">¿Quiénes somos?</a></li>
                <li>
                    <?php if ($creditos >= UMBRAL_BAJO_CREDITOS): // Usamos la constante ?>
                        <a href="upload_2/principal_upload.php" class="btn btn-primary" >Descargas</a>
                    <?php else: ?>
                        <a href="#" class="btn btn-disabled" title="No tienes suficientes créditos">Descargas</a>
                    <?php endif; ?>
                </li>
                <li><a href="login.php" class="logout">Salir</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="hero">
    <div class="hero-content">
        <h1> Sistema de <br><span>Administración de documentos</span></h1>
        <p>Comodidad sin precedentes. Acceda a la documentación esencial de su hijo al instante, demostrando cómo la tecnología simplifica su vida y fortalece la comunicación escolar.</p>
        <?php if ($creditos >= UMBRAL_BAJO_CREDITOS): // Usamos la constante ?>
                <a href="upload_2/principal_upload.php" class="btn btn-gold" >Descargas</a>
            <?php else: ?>
                <a href="upload_2/principal_upload" class="btn btn-disabled" title="No tienes suficientes créditos">Descargas</a>
            <?php endif; ?>
    </div>
    <div class="hero-image">
        <img src="images/kids/logo_archivos.jpg" alt="Imagen de portada" style="border-radius: 10px; width: 80%; height: auto;" >
    </div>
</section>

<div class="slider-frame">
    <ul>
        <li>
            <div class="slide-content">
                <img src="images/kids/graduados.jpg" alt="Graduados">
                <div class="text-box">
                    <h3>Excelencia Académica</h3>
                    <p>Fomentamos el amor por el aprendizaje y celebramos cada logro de nuestros estudiantes.</p>
                </div>
            </div>
        </li>
        <li>
            <div class="slide-content">
                <img src="images/kids/ciencia_1.jpg" alt="Ciencia">
                <div class="text-box">
                    <h3>Innovación y Ciencia</h3>
                    <p>Despertamos la curiosidad científica con laboratorios prácticos y tecnología moderna.</p>
                </div>
            </div>
        </li>
        <li>
            <div class="slide-content">
                <img src="images/kids/pequeños.jpg" alt="Niños pequeños">
                <div class="text-box">
                    <h3>Educación Inicial</h3>
                    <p>Un ambiente seguro y amoroso para los primeros pasos en el mundo del saber.</p>
                </div>
            </div>
        </li>
        <li>
            <div class="slide-content">
                <img src="images/kids/siembra.jpg" alt="Siembra">
                <div class="text-box">
                    <h3>Conciencia Ecológica</h3>
                    <p>Enseñamos a cuidar el planeta a través de actividades de siembra y reciclaje.</p>
                </div>
            </div>
        </li>
    </ul>
</div>
          
<section class="info fade-in">
    <div class="info-image">
        <img src="images/kids/robot.png" alt="Sobre la escuela" style="border-radius: 10px; width: 60%; height: auto;">
    </div>
    <div class="info-text">
        <h2>Sobre la escuela</h2>
        <p>
            Somos una institución comprometida con la excelencia educativa y los valores ciudadanos. 
            Promovemos el desarrollo integral de nuestros estudiantes con dedicación, esfuerzo y amor.
        </p>
        <a href="about.php" class="btn btn-outline">Leer más</a>
    </div>
</section>

<footer class="footer">
    <h3>Contacto</h3>
    <div class="contact-links">
        <a href="https://maps.app.goo.gl/Gez4D2tTMvxngbut5"><i class="fa fa-map-marker"></i> Santa Cruz, Aragua</a>
        <a href="#"><i class="fa fa-phone"></i> +58 414-1234567</a>
        <a href="mailto:correo@colegio.edu.ve"><i class="fa fa-envelope"></i> correo@colegio.edu.ve</a>
    </div>
    <p>© 2025 CEIP Luisa Cáceres de Arismendi</p>
</footer>

<script>
window.addEventListener('scroll', () => {
    document.querySelectorAll('.fade-in').forEach(el => {
        if (el.getBoundingClientRect().top < window.innerHeight - 100) {
            el.classList.add('visible');
        }
    });
});
</script>

<script>
    const mostrarAvisoCreditos = <?php echo json_encode($mostrar_aviso); ?>;
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const aviso = document.getElementById('avisoCreditos');
        const botonCerrar = document.getElementById('cerrarAviso');
        const tiempoEspera = 10000; // 10,000 milisegundos = 10 segundos
        
        // 1. Solo mostrar si la bandera PHP es TRUE
        if (mostrarAvisoCreditos === true) { 
            aviso.style.display = 'block';

            // 2. Temporizador: Habilitar el botón de cerrar después de 10 segundos
            let segundosRestantes = 10;
            botonCerrar.textContent = `Cerrar (Esperando ${segundosRestantes}s)`;
            
            const countdown = setInterval(() => {
                segundosRestantes--;
                if (segundosRestantes > 0) {
                    botonCerrar.textContent = `Cerrar (Esperando ${segundosRestantes}s)`;
                } else {
                    clearInterval(countdown);
                    botonCerrar.disabled = false;
                    botonCerrar.textContent = 'Cerrar'; // Cambiar texto
                }
            }, 1000);

            // 3. Función de cerrar (solo funciona si está habilitado)
            botonCerrar.addEventListener('click', () => {
                if (!botonCerrar.disabled) {
                    aviso.style.opacity = '0';
                    setTimeout(() => {
                        aviso.style.display = 'none';
                    }, 300); // Dar un pequeño fade-out visual
                }
            });
        }
    });
</script>
</body>
</html>