<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/components/header.php'); ?>

<style>
/* ===== Search Page (scoped) ===== */
.search-page { padding: 32px 0 48px; background: #f8f9fb; }
.search-page .search-head {
  display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:18px;
}
.search-page .search-title { margin:0; font-weight:700; letter-spacing:.2px; }
.search-page .search-meta { color:#6c757d; font-size:.95rem; }
.search-page .search-form-inline {
  display:flex; gap:10px; align-items:center; padding:10px; background:#fff; border:1px solid #e9ecef; border-radius:12px;
}
.search-page .search-form-inline input[type="text"] {
  border:none; outline:none; box-shadow:none; padding:8px 10px; flex:1; font-size:1rem;
}
.search-page .search-form-inline .btn { white-space:nowrap; }
.search-page .result-card {
  border:1px solid #eef0f4; border-radius:14px; overflow:hidden; background:#fff; height:100%; transition:transform .12s ease, box-shadow .12s ease;
}
.search-page .result-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(16,24,40,.06); }
.search-page .thumb-wrap { aspect-ratio: 16/9; background:#f1f3f5; overflow:hidden; }
.search-page .thumb-wrap img { width:100%; height:100%; object-fit:cover; display:block; }
.search-page .card-body { padding:14px 14px 16px; }
.search-page .badge-cat { font-size:.72rem; background:#eef4ff; color:#3451e7; border:1px solid #dbe4ff; }
.search-page .card-title { font-size:1rem; font-weight:600; line-height:1.35; margin:8px 0 0; color:#111827; }
.search-page .card-title mark { background: #fff2ac; padding: 0 .15em; border-radius: 4px; }
.search-page .empty {
  background:#fff; border:1px dashed #e2e8f0; border-radius:14px; padding:28px; text-align:center;
}
.search-page .empty i { font-size:2rem; color:#adb5bd; }
.search-page .pill {
  display:inline-block; padding:.35rem .7rem; border:1px solid #e9ecef; border-radius:999px; background:#fff; margin:4px; font-size:.9rem;
}
.search-page .pagination .page-link { border-radius:10px; margin:0 4px; }
@media (max-width: 575.98px) {
  .search-page .search-head { flex-direction:column; align-items:flex-start; gap:10px; }
}
</style>

<div class="search-page">
  <div class="container">

    <?php
      // Helper to safely highlight query terms in a string
      $highlight = function(string $text, string $q) {
          $safe = esc($text);
          $q = trim($q);
          if ($q === '') return $safe;

          // Build tokens from query (ignore 1-char tokens)
          $tokens = array_filter(preg_split('/\s+/u', $q), fn($t) => mb_strlen($t) > 1);
          if (empty($tokens)) return $safe;

          // Sort longer tokens first to avoid partial overlaps
          usort($tokens, fn($a,$b) => mb_strlen($b) <=> mb_strlen($a));
          $pattern = '~(' . implode('|', array_map(fn($t) => preg_quote($t, '~'), $tokens)) . ')~iu';

          // Wrap with <mark>
          return preg_replace($pattern, '<mark>$1</mark>', $safe);
      };

      $query    = $query   ?? '';
      $tag      = $tag     ?? '';              // NEW: current tag (if tag mode)
      $isTag    = trim($tag) !== '';           // NEW: are we filtering by a tag?
      $total    = (int) ($total ?? 0);
      $page     = max(1, (int) ($page ?? 1));
      $perPage  = max(1, (int) ($perPage ?? 10));
      $pages    = max(1, (int) ceil(($total ?: 0) / $perPage));

      // Heading text
      $heading  = $isTag ? ('Posts tagged: “' . esc($tag) . '”')
                         : ('Search results for: “' . esc($query) . '”');
    ?>

    <!-- Header row: Title + Inline search box -->
    <div class="search-head">
      <div>
        <h1 class="search-title h4"><?= $heading ?></h1>
        <div class="search-meta">
          <?= number_format($total) ?> result<?= $total===1 ? '' : 's' ?> <?= $total ? "• Page {$page} of {$pages}" : '' ?>
        </div>

        <?php if ($isTag): ?>
          <div class="mt-2">
            <span class="pill">Tag: <?= esc($tag) ?></span>
          </div>
        <?php endif; ?>
      </div>

      <!-- Inline search (submits back to /search) -->
      <form class="search-form-inline" action="<?= base_url('search'); ?>" method="get">
        <i class="fas fa-search ms-1 me-1"></i>
        <input type="text" name="q" value="<?= $isTag ? '' : esc($query) ?>" placeholder="Search articles, topics…" autofocus>
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-search me-1"></i> Search
        </button>
      </form>
    </div>

    <?php if (empty($results)): ?>
      <!-- Empty state -->
      <div class="empty">
        <div class="mb-2"><i class="far fa-folder-open"></i></div>
        <h5 class="mb-1">No results found</h5>
        <p class="text-muted mb-3">
          <?php if ($isTag): ?>
            We couldn’t find posts for the tag “<?= esc($tag) ?>”.
          <?php else: ?>
            Try different keywords, or explore popular categories below.
          <?php endif; ?>
        </p>
        <div>
          <a href="<?= base_url('news') ?>" class="pill">News</a>
          <a href="<?= base_url('entertainment') ?>" class="pill">Entertainment</a>
          <a href="<?= base_url('sports') ?>" class="pill">Sports</a>
          <a href="<?= base_url('technology') ?>" class="pill">Technology</a>
          <a href="<?= base_url('lifestyle') ?>" class="pill">Lifestyle</a>
          <a href="<?= base_url('health-fitness') ?>" class="pill">Health & Fitness</a>
        </div>
      </div>

    <?php else: ?>
      <!-- Grid of results -->
      <div class="row g-3">
        <?php foreach ($results as $r): ?>
          <div class="col-12 col-md-6 col-lg-4">
            <a href="<?= esc($r['blog_detail_url']) ?>" class="text-decoration-none">
              <div class="result-card">
                <div class="thumb-wrap">
                  <img
                    src="<?= esc($r['thumbnail_url']) ?>"
                    alt="<?= esc($r['post_title']) ?>"
                    loading="lazy"
                  >
                </div>
                <div class="card-body">
                  <?php if (!empty($r['category_slug'])): ?>
                    <span class="badge badge-cat"><?= esc($r['category_slug']) ?></span>
                  <?php endif; ?>
                  <h2 class="card-title">
                    <?= $highlight($r['post_title'] ?? '', $isTag ? '' : $query) ?>
                  </h2>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if ($pages > 1): ?>
        <nav class="mt-4 d-flex justify-content-center">
          <ul class="pagination">
            <?php
              // helper to build page url preserving mode (q or tag) + per_page
              $buildUrl = function($p) use ($query, $perPage, $isTag, $tag) {
                  $params = ['page' => (int)$p, 'per_page' => (int)$perPage];
                  if ($isTag) { $params['tag'] = $tag; } else { $params['q'] = $query; }
                  return base_url('search') . '?' . http_build_query($params);
              };
            ?>
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= $page > 1 ? $buildUrl($page - 1) : '#' ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <?php
              // compact pagination window
              $start = max(1, $page - 2);
              $end   = min($pages, $page + 2);
              if ($start > 1) {
                  echo '<li class="page-item"><a class="page-link" href="'.$buildUrl(1).'">1</a></li>';
                  if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
              }
              for ($p = $start; $p <= $end; $p++):
            ?>
              <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                <a class="page-link" href="<?= $buildUrl($p) ?>"><?= $p ?></a>
              </li>
            <?php endfor;
              if ($end < $pages) {
                  if ($end < $pages - 1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                  echo '<li class="page-item"><a class="page-link" href="'.$buildUrl($pages).'">'.$pages.'</a></li>';
              }
            ?>
            <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= $page < $pages ? $buildUrl($page + 1) : '#' ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    <?php endif; ?>

  </div>
</div>

<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/components/footer.php'); ?>

<script>
// Optional: show the toast message your header script expects
(function() {
  try {
    const params = new URLSearchParams(location.search);
    const q   = params.get('q');
    const tag = params.get('tag'); // NEW
    const url = new URL(location.href);

    // Your header script reads "search_query"—set it from q or tag for consistency.
    const label = tag ? `tag:${tag}` : (q || '');
    if (label && !params.get('search_query')) {
      params.set('search_query', label);
      url.search = params.toString();
      history.replaceState({}, '', url.toString());
    }
  } catch(e) {}
})();
</script>
