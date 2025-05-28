<?php include_once 'helpers/head.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bark & Wiggle – Home</title>

  <!-- vendors -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

  <!-- font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --purple-dark:#7a1ea1;
      --purple-light:#9c27b0;
      --gold:#ffd700;
      --bg:#fffdfc;
    }
    body{font-family:'Poppins',sans-serif;background:var(--bg);overflow-x:hidden;}

    /* ---------- NAV ---------- */
    .navbar{background:var(--purple-dark);}
    .navbar-brand,.navbar-nav .nav-link{color:#fff!important;font-weight:600;}
    .navbar-brand img{height:36px;margin-right:8px;}

    /* ---------- HERO ---------- */
    .hero{
      position:relative;min-height:100vh;min-height:640px;
      background:url('img/2-golden-retriever-puppies-landscape.jpg') center/cover no-repeat;
      display:flex;flex-direction:column;justify-content:center;align-items:center;
      text-align:center;color:#fff;padding:0 6vw;overflow:hidden;
    }
    .hero::before{
      content:'';position:absolute;inset:0;z-index: 0;
      background:linear-gradient(135deg,rgba(121, 53, 160, 0.47),rgba(155, 85, 177, 0.53));
    }
    .hero>*{position:relative;z-index:1}
    .hero h1{
      font-size:clamp(2.25rem,6vw,4.5rem);
      font-weight:700;margin:0 0 .3rem;text-transform:uppercase;
    }
    .hero p{font-size:1.1rem;max-width:760px;margin:0 auto;}
    .btn-book{
      margin-top:2rem;padding:14px 40px;font-weight:700;
      z-index: 2;
      background:var(--gold);border:none;color:#000;border-radius:60px;
      box-shadow:0 6px 20px rgba(0,0,0,.25);transition:.35s;
    }
    .btn-book:hover{transform:translateY(-4px);}

    /* watermark pup */
    .hero-logo{
      position:absolute;right:4%;bottom:8%;width:160px;
      opacity:.12;pointer-events:none;
    }

    /* paw bokeh */
    .paw{position:absolute;font-size:4rem;color:rgba(255,255,255,.12);pointer-events:none;
         animation:drift 26s linear infinite, pulse 7s ease-in-out infinite alternate;}
    .paw-tl{top:12%;left:6%;}
    .paw-tr{top:18%;right:8%;animation-duration:30s;}
    .paw-bl{bottom:18%;left:12%;animation-duration:28s;}
    .paw-br{bottom:14%;right:6%;animation-duration:32s;}
    @keyframes drift{100%{transform:translateY(-140%) rotate(360deg);}}
    @keyframes pulse{from{opacity:.12;transform:scale(.9);}to{opacity:.22;transform:scale(1.15);}}

    /* ---------- ASSURANCE ---------- */
    .assurance{padding:100px 10vw;text-align:center;}
    .assurance h2{font-weight:700;color:var(--purple-dark);margin-bottom:1.2rem;}
    .assurance p{max-width:880px;margin:auto;font-size:1.05rem;}

    /* ---------- SERVICES ---------- */
    .services{padding:80px 6vw;background:#fff;}
    .services h2{text-align:center;font-weight:700;color:var(--purple-dark);margin-bottom:2.5rem;}
    .card-deck{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:28px;}
    .service-card{
      position:relative;border-radius:1.2rem;overflow:hidden;height:360px;
      box-shadow:0 10px 30px rgba(0,0,0,.08);color:#fff;
    }
    .service-card img{object-fit:cover;width:100%;height:100%;filter:brightness(.63);}
    .service-card .content{
      position:absolute;inset:0;padding:1.4rem;display:flex;flex-direction:column;
      justify-content:flex-end;text-align:center;
    }
    .service-card h4{font-weight:700;font-size:1.25rem;margin-bottom:.35rem;text-transform:uppercase;}
    .service-card p{font-size:.9rem;line-height:1.45em;}

    /* ---------- MAP ---------- */
    .map-wrap{
      background:var(--purple-dark);color:#fff;text-align:center;padding:90px 0;
      clip-path:polygon(0 0,100% 6%,100% 100%,0 94%);
    }
    .map-wrap h2{font-weight:700;font-size:clamp(2rem,5vw,3rem);margin-bottom:1.6rem;}
    .ratio{max-width:900px;margin:auto;border:6px solid #fff;border-radius:1rem;overflow:hidden;}
    .btn-map{
      display:inline-block;margin-top:24px;padding:12px 32px;
      background:#fff;color:var(--purple-dark);font-weight:700;border-radius:50px;
      transition:.35s;
    }
    .btn-map:hover{transform:translateY(-4px);}

    /* ---------- FOOTER ---------- */
    footer{background:#f8f9fa;text-align:center;font-size:.9rem;color:#555;padding:30px 0;}

    /* mobile tweaks */
    @media(max-width:576px){
      .navbar-brand img{height:30px;}
      .hero-logo{width:110px;right:4%;bottom:10%;}
      .service-card{height:320px;}
      .assurance{padding:90px 6vw;}
    }
  </style>
</head>
<body>

<!-- ===== NAV ===== -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <!-- logo image + word-mark -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <!-- <img src="img/logo_cropped.png" alt="Bark & Wiggle"> -->
      Bark<span class="text-warning">&amp;</span>Wiggle
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon bg-light"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="nav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="user/user-login.php">Book&nbsp;Now</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ===== HERO ===== -->
<header class="hero">
  <img src="img/logo no text.png" class="hero-logo" alt="" aria-hidden="true">
  <h1>Premium Grooming • Gentle Care</h1>
  <p>Your pet’s spa day—executed with silky shampoos, breed-specific styling, and heaps of cuddles.</p>
  <a href="user/user-login.php" class="btn-book">Book&nbsp;Now</a>

  <!-- drifting paws -->
  <i class="fa-solid fa-paw paw paw-tl"></i>
  <i class="fa-solid fa-paw paw paw-tr"></i>
  <i class="fa-solid fa-paw paw paw-bl"></i>
  <i class="fa-solid fa-paw paw paw-br"></i>
</header>

<!-- ===== ASSURANCE ===== -->
<section class="assurance">
  <h2>Why Bark &amp; Wiggle?</h2>
  <p>
    We’re not just another grooming joint—we’re a <strong>wellness lounge</strong> for pets and their humans.
    From hypo-allergenic shampoos to whisper-quiet dryers, every detail is chosen for comfort and safety.
    Baristas craft your cup of coffee while certified groomers craft your buddy’s best look. 100 % love, 0 % shortcuts.
  </p>
</section>

<!-- ===== SERVICES ===== -->
<section class="services">
  <h2>Our Signature Treatments</h2>

  <div class="card-deck mb-5"><!-- DOG CARDS -->
    <div class="service-card" data-gsap>
      <img src="img/happy-chihuahua-4.3.jpg" alt="">
      <div class="content">
        <h4>Full Groom – Dog</h4>
        <p>Breed-specific styling, warm bath, blueberry facial, paw-dicure, and cologne finish for show-dog shine.</p>
      </div>
    </div>

    <div class="service-card" data-gsap>
      <img src="img/4.3-2-puppies-bonding.jpg" alt="">
      <div class="content">
        <h4>Paw &amp; Nail Spa</h4>
        <p>Precision clipping, paw-pad trim, and organic balm for silky-soft steps on any runway—or kitchen floor.</p>
      </div>
    </div>

    <div class="service-card" data-gsap>
      <img src="img/4.3-Close-up-canine-snoot.jpg" alt="">
      <div class="content">
        <h4>Dental Fresh</h4>
        <p>Enzymatic brushing and breath-buster gel keep kisses sweet and plaque at bay.</p>
      </div>
    </div>
  </div>

  <div class="card-deck"><!-- CAT CARDS -->
    <div class="service-card" data-gsap>
      <img src="img/cat2.jpg" alt="">
      <div class="content">
        <h4>Full Groom – Cat</h4>
        <p>Hypo-allergenic bath, de-shed blow-out, coat conditioning, and gentle paw trim for a purr-fect glow.</p>
      </div>
    </div>

    <div class="service-card" data-gsap>
      <img src="img/cat3.jpg" alt="">
      <div class="content">
        <h4>Sanitary Trim</h4>
        <p>Discreet clipping that keeps litter tracked out and feline dignity fully intact.</p>
      </div>
    </div>

    <div class="service-card" data-gsap>
      <img src="img/dog3.jpg" alt="">
      <div class="content">
        <h4>De-Shed &amp; Blow-out</h4>
        <p>High-velocity dryer + specialized tools remove loose undercoat—bye-bye tumble-fur!</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== MAP ===== -->
<section class="map-wrap">
  <h2>Tap the pin, clean the fur baby & grab a nice coffee</h2>
  <div class="ratio ratio-16x9">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d573.5391799243392!2d121.01764597515296!3d14.7523281912084!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b1b46a90fb83%3A0xbf26ce677d39ac4f!2sFive%20J%E2%80%99s%20Property%20Leasing!5e0!3m2!1sen!2sph!4v1748449852313!5m2!1sen!2sph"
            style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
  <a href="https://maps.app.goo.gl/sjEoWpg3HV3svaXK8" target="_blank" class="btn-map">Get&nbsp;Directions</a>
</section>

<!-- ===== FOOTER ===== -->
<footer>
  📍 Five J's Building, Bagumbong Deparo Rd., Caloocan  |  📞 +63 927 990 2111  |  Follow us on Facebook &amp; Viber
</footer>

<!-- scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* GSAP Animations */
gsap.registerPlugin(ScrollTrigger);

/* hero elements */
gsap.from(".hero h1, .hero p", {opacity:0,y:40,duration:1,stagger:.15});

/* service cards */
gsap.utils.toArray("[data-gsap]").forEach(el=>{
  gsap.from(el,{opacity:0,y:60,scale:.92,duration:.85,
    scrollTrigger:{trigger:el,start:"top 85%"}});
});
</script>
</body>
</html>
