            </main>

            <!-- Footer -->
            <footer class="border-t border-accent/20 bg-white px-6 py-4">
                <div class="flex items-center justify-between text-sm text-charcoal-500">
                    <p>&copy; <?= date('Y') ?> A Casa do Gi. Painel de Administração.</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const dropdowns = document.querySelectorAll('[data-dropdown-toggle]');
            dropdowns.forEach(function(dropdown) {
                const menu = dropdown.nextElementSibling;
                if (menu && !dropdown.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });

        // CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Helper function for AJAX requests
        async function apiRequest(url, method = 'GET', data = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            };

            if (data && method !== 'GET') {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);
            return response.json();
        }

        // Confirm delete helper
        function confirmDelete(message = 'Tem a certeza que deseja eliminar?') {
            return confirm(message);
        }
    </script>

    <!-- Inactivity Auto-Logout (1 minute without mouse movement) -->
    <script>
    (function() {
        'use strict';
        var inactivityTimeout = 60000; // 1 minute in milliseconds
        var warningTimeout = 50000;    // Warning at 50 seconds
        var timer = null;
        var warningTimer = null;
        var warningShown = false;
        var warningOverlay = null;

        function createWarningOverlay() {
            warningOverlay = document.createElement('div');
            warningOverlay.id = 'inactivity-warning';
            warningOverlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;display:flex;align-items:center;justify-content:center;';
            warningOverlay.innerHTML = '<div style="background:#fff;border-radius:12px;padding:32px;max-width:400px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.3);">' +
                '<svg style="width:48px;height:48px;color:#dc2626;margin:0 auto 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>' +
                '<h3 style="font-size:18px;font-weight:700;color:#1f2937;margin-bottom:8px;">Sessao prestes a expirar</h3>' +
                '<p style="color:#6b7280;margin-bottom:16px;">Mova o rato para manter a sessao ativa.</p>' +
                '<div id="inactivity-countdown" style="font-size:24px;font-weight:700;color:#dc2626;">10</div>' +
                '</div>';
            document.body.appendChild(warningOverlay);
        }

        function removeWarning() {
            if (warningOverlay && warningOverlay.parentNode) {
                warningOverlay.parentNode.removeChild(warningOverlay);
                warningOverlay = null;
            }
            warningShown = false;
        }

        function doLogout() {
            window.location.href = '<?= basePath() ?>/admin/logout.php';
        }

        function showWarning() {
            warningShown = true;
            createWarningOverlay();
            var seconds = 10;
            var countdownEl = document.getElementById('inactivity-countdown');
            var countdownInterval = setInterval(function() {
                seconds--;
                if (countdownEl) countdownEl.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(countdownInterval);
                    doLogout();
                }
            }, 1000);

            // Store interval so we can clear it on activity
            warningOverlay._countdownInterval = countdownInterval;
        }

        function resetTimers() {
            clearTimeout(timer);
            clearTimeout(warningTimer);

            if (warningShown && warningOverlay) {
                if (warningOverlay._countdownInterval) {
                    clearInterval(warningOverlay._countdownInterval);
                }
                removeWarning();
            }

            warningTimer = setTimeout(showWarning, warningTimeout);
            timer = setTimeout(doLogout, inactivityTimeout);
        }

        // Track mouse movement, clicks, keyboard, scroll, touch
        var events = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'];
        events.forEach(function(evt) {
            document.addEventListener(evt, resetTimers, { passive: true });
        });

        // Start timers
        resetTimers();
    })();
    </script>

    <?php if (isset($pageScripts)): ?>
    <?= $pageScripts ?>
    <?php endif; ?>
</body>
</html>
