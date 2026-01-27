            </main>

            <!-- Footer -->
            <footer class="border-t border-accent/20 bg-white px-6 py-4">
                <div class="flex items-center justify-between text-sm text-charcoal-500">
                    <p>&copy; <?= date('Y') ?> A Casa do Gi. Painel de Administracao.</p>
                    <p>v1.0.0</p>
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

    <?php if (isset($pageScripts)): ?>
    <?= $pageScripts ?>
    <?php endif; ?>
</body>
</html>
