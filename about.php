<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}
$creditos = $_SESSION['creditos'];
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css2/about_clean.css">
  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Raleway:400,600&display=swap" rel="stylesheet">
  <!-- font wesome stylesheet -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LCDA - Página principal</title>
</head>


<body>



 
<header class="header">
  <p class="welcome">Bienvenido, <?php echo $_SESSION['nombre_usuario']; ?></p>

  <div class="topbar">
    <div class="logo">
      <img src="miscelaneos/logo_header.jpg" alt="Logo del colegio">
      <h1>U.E.P. <span>LUISA CÁCERES DE ARISMENDI</span></h1>
    </div>

    <nav class="navbar">
      <ul>
        <li><a href="index.php" class="">Inicio</a></li>
        <li><a href="about.php">¿Quiénes somos?</a></li>
        <li>
          <?php if ($creditos >= 60): ?>
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





<div class="about-section">
  <h1>Acerca de nuestra institución</h1>
  <p>En la U.E.P. Luisa Cáceres de Arismendi, entendemos que el futuro de su hijo se construye hoy. 
        Nuestro modelo educativo es <strong>integral y vanguardista</strong>, diseñado para ir más allá de los límites tradicionales. 
        Nos enfocamos en cultivar el talento único de cada estudiante, preparándolos no solo para la universidad, sino para ser <strong>líderes creativos</strong> en un mundo en constante cambio.</p>
</div>


<div class="encuadre-m-v">
  <div class="card_1">
  <h3> <img src="images/mision.png" alt="Misión" style="width:10%; height:10%;">Misión</h3>

  <p>Proveer a los estudiantes de una educación integral, fomentando valores, habilidades y conocimientos que les permitan desarrollarse como ciudadanos responsables y comprometidos con su entorno.</p>
    
</div>

  <div class="card_1">
  <h3> <img src="images/vision.png" alt="Visión" style="width:10%; height:10%;">Visión</h3>
  <p>Ser una institución educativa reconocida por su excelencia académica, innovación pedagógica y compromiso con la formación de individuos capaces de enfrentar los desafíos del siglo XXI.</p>
    
</div>
</div>



<h2 style="text-align:center">NUESTRO EQUIPO</h2>
<div class="row">
  <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>Jane Doe</h2>
        <p class="title">CEO & Founder</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>jane@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

  <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>Mike Ross</h2>
        <p class="title">Art Director</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>mike@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

  <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

  <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

  <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

  <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

  <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

  <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
        <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

  <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
       <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

   <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
       <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

   <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
       <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

   <div class="column">
    <div class="card">
      <img src="images/teacher_icon.png" alt="Jane" style="width:100%">
      <div class="container">
        <h2>John Doe</h2>
        <p class="title">Designer</p>
        <p>Some text that describes me lorem ipsum ipsum lorem.</p>
       <p>john@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>


</div>



<section class="map-section fade-in">
    <h2>¿Dónde encontrarnos?</h2>
    
    <div class="map-container">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d200.70953936990393!2d-67.51230198089408!3d10.17604471902047!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e83f2a170562ff9%3A0x892a543f4a9b31d8!2sCon%20las%20Pilas%20Puestas!5e0!3m2!1ses!2sve!4v1701389028888!5m2!1ses!2sve"
            width="100%" 
            height="500" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>

<footer class="footer_section">
   
            <h4>
              Contacto
            </h4>
            <div class="contact_link_box">
              <a href="https://maps.app.goo.gl/Gez4D2tTMvxngbut5">
                <i class="fa fa-map-marker" aria-hidden="true"></i>
                <span>
                  Dirección: Calle prolongación mariño, 09-34, Santa Cruz 2122, Aragua
                 <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d499.8303806263648!2d-67.51214010372348!3d10.17609680222399!3m2!1i1024!2i768!4f13.1!5e1!3m2!1ses-419!2sve!4v1759675687068!5m2!1ses-419!2sve" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                 -->
                </span>
              </a>
              <a href="">
                <i class="fa fa-phone" aria-hidden="true"></i>
                <span>
                  Llamar +58 414-1234567
                </span>
              </a>
              <a href="">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <span>
                  Email:
                  </span>
                  </a>
                  </div>
                  </footer>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>

</html>