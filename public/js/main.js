(function () {
    function abrirModal(id) {
        var modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function cerrarModal(modal) {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-modal-open]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            abrirModal(btn.getAttribute('data-modal-open'));
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(function (el) {
        el.addEventListener('click', function () {
            var modal = el.closest('.modal');
            cerrarModal(modal);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.is-open').forEach(cerrarModal);
        }
    });

    document.querySelectorAll('.modal.is-open').forEach(function (modal) {
        document.body.style.overflow = 'hidden';
    });
})();
