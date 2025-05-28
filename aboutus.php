<?php include_once 'helpers/head.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bark & Wiggle – About</title>

  <!-- vendors -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

  <style>
    :root{
      --purple-dark:#7A1EA1;
      --purple-light:#9C27B0;
      --gold:#FFD700;
      --bg:#fdfdff;
    }
    body{font-family:'Poppins',sans-serif;background:var(--bg);overflow-x:hidden;}

    /* NAV */
    .navbar{background:var(--purple-dark);}
    .navbar-brand,.navbar-nav .nav-link{color:#fff!important;font-weight:600;}

    /* HERO */
    .hero{
      position:relative;
      background:linear-gradient(135deg,var(--purple-dark),var(--purple-light));
      padding:140px 0 200px;
      color:#fff;text-align:center;overflow:hidden;
    }
    .hero h1{
      font-family:'Bebas Neue',cursive;
      font-size:clamp(3rem,8vw,7rem);
      letter-spacing:.03em;text-transform:uppercase;
      animation:text-flicker 4s linear infinite;
    }
    @keyframes text-flicker{
      0%,18%,22%,25%,53%,57%,100%{opacity:1}
      20%,24%,55%{opacity:0.35}
    }

    /* PAW FLOAT */
    .paw{position:absolute;font-size:3.5rem;opacity:.12;color:#fff;animation:float 18s linear infinite;}
    .paw:nth-child(2){top:20%;left:80%;animation-duration:24s;}
    .paw:nth-child(3){top:60%;left:10%;animation-duration:20s;}
    .paw:nth-child(4){top:78%;left:68%;animation-duration:26s;}
    @keyframes float{100%{transform:translateY(-120vh) rotate(360deg);} }

    /* STATIC BANNER  (replaces scrolling ticker) */
    .banner{
      position:absolute;bottom:0;left:0;width:100%;
      background:var(--purple-dark);padding:10px 0;border-top:2px solid rgba(255,255,255,.25);
    }
    .banner p{
      margin:0;font-family:'Playfair Display',serif;
      font-size:1.75rem;font-weight:700;color:var(--gold);
      white-space:normal;
    }

    /* INFO CARDS */
    .info-card{
      background:#fff;border-radius:1rem;padding:2rem 1.5rem;
      box-shadow:0 14px 30px rgba(0,0,0,.06);
      transition:.4s;height:100%;
    }
    .info-card:hover{transform:translateY(-8px) scale(1.02);}
    .info-card h4{color:var(--purple-light);font-weight:700;text-transform:uppercase;font-size:1.3rem;margin-bottom:.7rem;}
    .info-card i{color:var(--gold);margin-right:.55rem;}

    /* CONTACT */
    .contact-banner{
      display:flex;flex-direction:column;align-items:center;justify-content:center;
      text-align:center;background:var(--purple-dark);color:#fff;
      padding:80px 20px;margin-top:80px;position:relative;overflow:hidden;
    }
    .contact-banner h2{
      font-family:'Bebas Neue',cursive;font-size:clamp(2.5rem,6vw,5rem);
      text-shadow:2px 2px 6px rgba(0,0,0,.3);
      letter-spacing:.04em;margin-bottom:1.2rem;
    }
    .contact-btn{
      margin:6px 8px;padding:14px 26px;font-size:1.1rem;font-weight:600;
      border:none;border-radius:60px;transition:.35s;
    }
    .btn-fb{background:#3b5998;color:#fff;}
    .btn-viber{background:#7360F2;color:#fff;}
    .contact-btn:hover{transform:translateY(-4px);}

    footer{background:#f8f9fa;padding:24px 0;text-align:center;font-size:.9rem;color:#555;}

    /* ====== MOBILE OPTIMISER (≤ 576px) ====== */
@media (max-width: 576px){

  /* NAV */
  .navbar-brand{font-size:1.25rem;}
  .navbar-toggler{padding:.25rem .5rem;}

  /* HERO */
  .hero{padding:100px 0 160px;}      /* tighten top spacing */
  .hero h1{
    font-size:2.75rem;               /* less jumbo */
    letter-spacing:.02em;
  }
  .hero p{font-size:1rem;}

  /* STATIC BANNER */
  .banner p{
    font-size:1.1rem;                /* keep headline readable */
    line-height:1.3;
    padding:0 12px;
  }

  /* PAW DECOR — hide to avoid clutter */
  .paw{display:none;}

  /* MAIN INFO GRID */
  .container .row>*{
    flex:0 0 100%;                   /* stack cards full-width */
    max-width:100%;
  }
  .info-card{
    padding:1.5rem 1rem;
    margin-bottom:1rem;              /* extra gap between stacked cards */
  }
  .info-card h4{font-size:1.1rem;}

  .contact-banner div{              /* the <div> that wraps the two buttons */
    display:flex;
    flex-direction:column;          /* vertical */
    gap:12px;                       /* even spacing */
    width:100%;
  }
  .contact-btn{
    width:100%;
    border-radius:8px;              /* normal rectangle corners on mobile */
    padding:14px 18px;
  }
}


  /* FOOTER */
  footer{padding:20px 10px;font-size:.8rem;}
}

  </style>
</head>
<body>

<!-- NAV -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">Bark<span class="text-warning">&</span>Wiggle</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon bg-light"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="user/user-login.php">Book Now</a></li>
        <li class="nav-item"><a class="nav-link active fw-bold" aria-current="page" href="#">About</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <h1 data-aos="zoom-out">Coffee ☕ meets&nbsp;Fur ✂️</h1>

  <!-- floating paws -->
  <i class="fa-solid fa-paw paw"></i>
  <i class="fa-solid fa-paw paw"></i>
  <i class="fa-solid fa-paw paw"></i>
  <i class="fa-solid fa-paw paw"></i>

  <!-- static banner -->
  <div class="banner">
    <p>Bark &amp; Wiggle • Pet Grooming • Specialty Coffee • Caloocan</p>
  </div>
</section>

<!-- MAIN INFO -->
<section class="container py-5">
  <div class="row g-4 align-items-stretch">

    <!-- Summary -->
    <div class="col-lg-6" data-aos="fade-right">
      <div class="info-card h-100">
        <h4><i class="fa-solid fa-lightbulb"></i>Our Story</h4>
        <p>
          Originally a cozy neighbourhood café (est. 2023), we pivoted in 2025 to add
          full-service pet grooming—because why not spoil both hoomans and fur-babies?
          Sip a latte while your pup gets the glow-up. Win-win! ☕🐾
        </p>
      </div>
    </div>

    <!-- Brands -->
    <div class="col-lg-3" data-aos="fade-up" data-aos-delay="100">
      <div class="info-card h-100">
        <h4><i class="fa-solid fa-bone"></i>Brands</h4>
        <p class="mb-2 fw-semibold">🐾 Bark &amp; Wiggle<br><small class="text-muted">Pet Grooming &amp; Store</small></p>
        <p class="fw-semibold">☕ JAPCafe<br><small class="text-muted">Cakes • Coffee • Pastries</small></p>
      </div>
    </div>

    <!-- Location & Hours -->
    <div class="col-lg-3" data-aos="fade-up" data-aos-delay="200">
      <div class="info-card h-100">
        <h4><i class="fa-solid fa-clock"></i>Visit Us</h4>
        <p class="mb-1 small">Five J’s Bldg, Bagumbong Deparo Rd., North Caloocan</p>
        <p class="small mb-2">
          Mon–Fri 8 AM–6 PM<br>
          Sat–Sun 7 : 30 AM–7 PM
        </p>
      </div>
    </div>

  </div>
</section>

<!-- CONTACT -->
<section class="contact-banner" data-aos="zoom-in">
  <h2>Contact us for more questions</h2>
  <div>
    <a href="https://www.facebook.com/profile.php?id=61573114647787" target="_blank" class="contact-btn btn-fb">
      <i class="fa-brands fa-facebook-f me-1"></i> Facebook
    </a>
    <a href="viber://chat?number=%2B639279902111" class="contact-btn btn-viber">
      <i class="fa-brands fa-viber me-1"></i> Viber&nbsp;0927&nbsp;990&nbsp;2111
    </a>
  </div>
</section>

<!-- FOOTER -->
<footer>
  📍 Five J's Building, Bagumbong Rd., Caloocan • 📞 +63 927 990 2111
</footer>

<!-- scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ once:true, duration:1000, offset:140 });</script>
</body>
</html>
