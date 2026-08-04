  </main>

  <footer class="admin-foot">
    <p>&copy; <?= date('Y') ?> <?= e(site_name()) ?> — Admin Panel</p>
    <p><?= e(SITE_URL) ?></p>
  </footer>
</div>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<script src="<?= e(asset('js/admin.js')) ?>" defer></script>
</body>
</html>
