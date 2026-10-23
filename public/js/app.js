(function () {
    var nodes = document.querySelectorAll('[data-pulse]');
    if (nodes.length) {
        var url = nodes[0].getAttribute('data-url');
        var baseline = (nodes[0].getAttribute('data-latest') || '') + '|' + (nodes[0].getAttribute('data-count') || '');

        window.setInterval(function () {
            fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    var signature = (data.latest || '') + '|' + (data.count || 0);
                    if (signature === baseline) {
                        return;
                    }
                    for (var i = 0; i < nodes.length; i++) {
                        nodes[i].classList.add('is-fresh');
                        var label = nodes[i].querySelector('[data-pulse-label]');
                        var reload = nodes[i].querySelector('[data-reload]');
                        if (label) {
                            label.textContent = 'Diperbarui baru saja';
                        }
                        if (reload) {
                            reload.hidden = false;
                        }
                    }
                })
                .catch(function () {});
        }, 15000);
    }

    var setupButtons = document.querySelectorAll('[data-setup]');
    for (var s = 0; s < setupButtons.length; s++) {
        setupButtons[s].addEventListener('click', function () {
            var name = this.getAttribute('data-setup');
            for (var i = 0; i < setupButtons.length; i++) {
                var on = setupButtons[i] === this;
                setupButtons[i].classList.toggle('is-active', on);
                setupButtons[i].setAttribute('aria-pressed', on ? 'true' : 'false');
            }
            var panels = document.querySelectorAll('[data-setup-panel]');
            for (var p = 0; p < panels.length; p++) {
                panels[p].hidden = panels[p].getAttribute('data-setup-panel') !== name;
            }
        });
    }

    document.addEventListener('click', function (event) {
        var reload = event.target.closest('[data-reload]');
        if (reload) {
            window.location.reload();
        }

        var copy = event.target.closest('[data-copy]');
        if (copy) {
            event.preventDefault();
            event.stopPropagation();
            var text = copy.getAttribute('data-copy');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text);
            }
            copy.classList.add('is-copied');
            window.setTimeout(function () { copy.classList.remove('is-copied'); }, 1200);
        }

        var demo = event.target.closest('[data-demo]');
        if (demo && !event.target.closest('[data-copy]')) {
            var email = document.querySelector('[data-login-email]');
            var password = document.querySelector('[data-login-password]');
            if (email && password) {
                email.value = demo.getAttribute('data-email');
                password.value = demo.getAttribute('data-password');
                email.focus();
            }
        }

        var toggle = event.target.closest('[data-toggle-password]');
        if (toggle) {
            var input = document.querySelector('[data-login-password]');
            if (!input) {
                return;
            }
            input.type = input.type === 'password' ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', input.type === 'text' ? 'true' : 'false');
        }

        var remove = event.target.closest('[data-remove-doc]');
        if (remove) {
            var holder = document.querySelector('[data-remove-holder]');
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'remove_documents[]';
            hidden.value = remove.getAttribute('data-remove-doc');
            holder.appendChild(hidden);
            remove.closest('.file-row').remove();
        }
    });

    var files = document.querySelector('[data-files]');
    var fileList = document.querySelector('[data-file-list]');
    if (files && fileList) {
        var renderFiles = function () {
            var names = [];
            for (var i = 0; i < files.files.length; i++) {
                names.push(files.files[i].name);
            }
            fileList.textContent = names.length ? names.join(', ') : '';
        };
        files.addEventListener('change', renderFiles);
        var zone = files.closest('.dropzone');
        if (zone) {
            zone.addEventListener('dragover', function (event) {
                event.preventDefault();
                zone.classList.add('is-hot');
            });
            zone.addEventListener('dragleave', function () {
                zone.classList.remove('is-hot');
            });
            zone.addEventListener('drop', function () {
                zone.classList.remove('is-hot');
            });
        }
    }
})();
