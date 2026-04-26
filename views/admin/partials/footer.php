<?php
/**
 * Admin Layout — Footer Partial
 * ──────────────────────────────
 * Based on Mazer Admin template
 */
?>
        </div><!-- end .page-content -->

        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <div class="float-start">
                    <p><?= date('Y') ?> &copy; <?= e(get_setting('general_store_name', 'Electro Store')) ?></p>
                </div>
                <div class="float-end">
                    <p>Powered by <span class="text-primary">Ecommerce CMS</span></p>
                </div>
            </div>
        </footer>
    </div><!-- end #main -->
</div><!-- end #app -->

<!-- Mazer JS -->
<script src="<?= asset('admin/static/js/initTheme.js') ?>"></script>
<script src="<?= asset('admin/static/js/components/dark.js') ?>"></script>
<script src="<?= asset('admin/extensions/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
<script src="<?= asset('admin/compiled/js/app.js') ?>"></script>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
