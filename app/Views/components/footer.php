<!-- ===== FOOTER SECTION ===== -->
<footer class="py-5" style="background:#0a2744;">
  <div class="container">
    <div class="row g-4">

      <!-- COL 1: Logo + About + Socials + Links + Email -->
      <div class="col-lg-3 col-md-6">

        <!-- Logo -->
        <a href="<?= base_url('/') ?>">
          <img src="<?= base_url('public/assest/images/Flypped-hindi-logo.webp') ?>"
               alt="Flypped Hindi Logo"
               height="60"
               class="mb-3 d-block">
        </a>

        <!-- About Text -->
        <p class="text-white small lh-base mb-4">
          <strong class="text-white">flyppedhindi</strong> एक डिजिटल मंच है, जो पाठकों को समाचार, स्वास्थ्य और फिटनेस से जुड़ी सलाह, लाइफस्टाइल, मनोरंजन की दुनिया की हलचल और अन्य ज्ञानवर्धक व रोचक विषयों पर सरल, सटीक और विश्वसनीय जानकारी प्रदान करता है। हमारा उद्देश्य है आपको हर विषय पर जागरूक और अपडेटेड रखना है - वह भी आपकी अपनी भाषा, हिंदी में। यह मंच न केवल भारत में, बल्कि विदेशों में बसे एनआरआई भारतीयों को भी अपनी जड़ों से जोड़े रखने का काम करता है।
        </p>

        <!-- Social Icons -->
        <div class="d-flex gap-2 mb-4">
          <a href="https://www.facebook.com/flyppedhindi/" target="_blank" rel="noopener"
             class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center p-0 text-decoration-none"
             style="width:35px;height:35px;" aria-label="Facebook">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="https://www.instagram.com/hindiflypped/" target="_blank" rel="noopener"
             class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center p-0 text-decoration-none"
             style="width:35px;height:35px;" aria-label="Instagram">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="https://x.com/flyppedhindi" target="_blank" rel="noopener"
             class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center p-0 text-decoration-none"
             style="width:35px;height:35px;" aria-label="Twitter">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="https://www.threads.net/@hindiflypped" target="_blank" rel="noopener"
             class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center p-0 text-decoration-none"
             style="width:35px;height:35px;" aria-label="Threads">
            <i class="fab fa-threads"></i>
          </a>
          <a href="https://www.youtube.com/@flyppedhindinews" target="_blank" rel="noopener"
             class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center p-0 text-decoration-none"
             style="width:35px;height:35px;" aria-label="YouTube">
            <i class="fab fa-youtube"></i>
          </a>
        </div>

        <!-- Quick Links -->
        <div class="mb-4">
          <a href="<?= base_url('contact') ?>" class="text-warning text-decoration-none fw-bold small text-uppercase">Contact Us</a>
          <span class="text-white mx-1">|</span>
          <a href="<?= base_url('write-for-us') ?>" class="text-warning text-decoration-none fw-bold small text-uppercase">Write For Us</a>
          <span class="text-white mx-1">|</span>
          <a href="<?= base_url('authors') ?>" class="text-warning text-decoration-none fw-bold small text-uppercase">Our Authors</a>
        </div>

        <!-- Email -->
        <p class="text-white fw-bold mb-2"><i class="fas fa-envelope me-1"></i> Get in Touch</p>
        <ul class="list-unstyled mb-0">
          <li class="mb-1"><a href="mailto:support@flyppedhindi.com" class="text-white-50 text-decoration-none small"><i class="fas fa-angle-right text-warning me-1"></i> support@flyppedhindi.com</a></li>
          <li class="mb-1"><a href="mailto:editors@flyppedhindi.com" class="text-white-50 text-decoration-none small"><i class="fas fa-angle-right text-warning me-1"></i> editors@flyppedhindi.com</a></li>
          <li class="mb-1"><a href="mailto:ads@flyppedhindi.com" class="text-white-50 text-decoration-none small"><i class="fas fa-angle-right text-warning me-1"></i> ads@flyppedhindi.com</a></li>
          <li><a href="mailto:info@flyppedhindi.com" class="text-white-50 text-decoration-none small"><i class="fas fa-angle-right text-warning me-1"></i> info@flyppedhindi.com</a></li>
        </ul>

      </div>

      <!-- COL 2: Latest Blog -->
      <div class="col-lg-3 col-md-6"> 
        <h5 class="text-warning fw-bold text-uppercase mb-4">Latest Blog</h5>

        <?php if (!empty($latest_footer_blogs)): ?>
          <?php foreach ($latest_footer_blogs as $blog): ?>
            <div class="mb-4">
              <span class="text-warning fw-bold text-uppercase small d-block mb-1">
                <?= htmlspecialchars($blog['category_name'] ?? 'News') ?>
              </span>
              <p class="mb-1">
                <a href="<?= base_url($blog['post_name'] ?? '') ?>" class="text-white text-decoration-underline-hover small lh-sm d-block" style="text-decoration: none;">
                  <?= htmlspecialchars($blog['post_title']) ?>
                </a>
              </p>
              <span class="text-white-50" style="font-size:12px;">
                <?= date('F d, Y', strtotime($blog['post_date'])) ?>
              </span>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-white-50 small">No recent blogs available.</p>
        <?php endif; ?>
      </div>

      <!-- COL 3 & 4: Number of Posts (Forced Side-by-Side Layout) -->
      <div class="col-lg-6 col-md-12">
        <h5 class="text-warning fw-bold text-uppercase mb-4">Number of Posts</h5>
        <div class="row gx-4"> <!-- Added gx-4 for slightly more horizontal gap -->
          
          <!-- First Column of Categories (Changed to col-6) -->
          <div class="col-6">
            <ul class="list-unstyled mb-0">
              <?php if (!empty($categories_column_1)): ?>
                <?php foreach ($categories_column_1 as $cat): ?>
                  <?php
                    $catName = html_entity_decode($cat['name']);
                    if (stripos($catName, 'Tech') !== false && stripos($catName, 'Gadgets') !== false) {
                        $catName = 'Tech & Gadgets';
                        $slug = 'tech-gadgets';
                    } else {
                        $slug = $cat['slug'] ?? strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $catName), '-'));
                    }
                  ?>
                  <li class="d-flex align-items-center justify-content-between mb-3 border-bottom border-secondary pb-2">
                    <a href="<?= base_url($slug) ?>" class="text-white text-decoration-none small text-uppercase"><?= $catName ?></a>
                    <span class="badge rounded-pill text-dark fw-bold" style="background:#f5c518;min-width:36px;">
                      <?= $cat['post_count'] ?>
                    </span>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>

          <!-- Second Column of Categories (Changed to col-6) -->
          <div class="col-6">
            <ul class="list-unstyled mb-0">
              <?php if (!empty($categories_column_2)): ?>
                <?php foreach ($categories_column_2 as $cat): ?>
                  <?php
                    $catName = html_entity_decode($cat['name']);
                    if (stripos($catName, 'Tech') !== false && stripos($catName, 'Gadgets') !== false) {
                        $catName = 'Tech & Gadgets';
                        $slug = 'tech-gadgets';
                    } else {
                        $slug = $cat['slug'] ?? strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $catName), '-'));
                    }
                  ?>
                  <li class="d-flex align-items-center justify-content-between mb-3 border-bottom border-secondary pb-2">
                    <a href="<?= base_url($slug) ?>" class="text-white text-decoration-none small text-uppercase"><?= $catName ?></a>
                    <span class="badge rounded-pill text-dark fw-bold" style="background:#f5c518;min-width:36px;">
                      <?= $cat['post_count'] ?>
                    </span>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>

              <!-- अन्य (Other) -->
              <li class="d-flex align-items-center justify-content-between mb-3 border-bottom border-secondary pb-2">
                <a href="<?= base_url('other') ?>" class="text-white text-decoration-none small text-uppercase">अन्य</a>
                <span class="badge rounded-pill text-dark fw-bold" style="background:#f5c518;min-width:36px;">
                  <?= isset($other_count) ? $other_count : '0' ?>
                </span>
              </li>
            </ul>
          </div>

        </div>
      </div>

    </div>
  </div>
</footer>

<!-- Bottom Bar -->
<footer class="py-3" style="background:#071d36;">
  <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
    <p class="text-white-50 small mb-0">&copy; <?= date('Y') ?> All rights reserved</p>
    <ul class="list-inline mb-0">
      <li class="list-inline-item"><a href="<?= base_url('/') ?>" class="text-white-50 text-decoration-none small text-uppercase">Home</a></li>
      <li class="list-inline-item text-white-50">|</li>
      <li class="list-inline-item"><a href="<?= base_url('disclaimer') ?>" class="text-white-50 text-decoration-none small text-uppercase">Disclaimer</a></li>
      <li class="list-inline-item text-white-50">|</li>
      <li class="list-inline-item"><a href="<?= base_url('privacy-policy') ?>" class="text-white-50 text-decoration-none small text-uppercase">Privacy Policy</a></li>
      <li class="list-inline-item text-white-50">|</li>
      <li class="list-inline-item"><a href="<?= base_url('sitemap') ?>" class="text-white-50 text-decoration-none small text-uppercase">Sitemap</a></li>
    </ul>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('public/assest/js/main.js') ?>"></script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7949838781204630" crossorigin="anonymous"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  if (localStorage.getItem("subscribed") || localStorage.getItem("popupClosed")) return;
  setTimeout(showPopup, 3000);
});

function showPopup() {
  if (!localStorage.getItem("subscribed") && !localStorage.getItem("popupClosed")) {
    document.getElementById("subscribePopup").style.display = "flex";
  }
}

function openSubscriptionPopup() {
  document.getElementById("subscribePopup").style.display = "flex";
}

function closePopup() {
  document.getElementById("subscribePopup").style.display = "none";
  localStorage.setItem("popupClosed", "true");
}

document.getElementById("subscribeForm").addEventListener("submit", function (e) {
  e.preventDefault();
  let email = document.getElementById("subscriberEmail").value;
  fetch("<?= base_url('subscribe') ?>", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email: email })
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if (data.status === "success" || data.status === "exists") {
      localStorage.setItem("subscribed", "true");
      closePopup();
    }
  })
  .catch(err => console.error(err));
});
</script>