document.addEventListener("DOMContentLoaded", function () {
    // Copy link handler
    document.querySelectorAll('.wpsb.copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const link = this.getAttribute('data-link');
            navigator.clipboard.writeText(link).then(() => {
                this.textContent = "Copied!";
                setTimeout(() => this.textContent = "Copy Link", 2000);
            });
        });
    });

    // Open all share links in a small popup
    document.querySelectorAll('.wpsb-share-buttons a').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.href;
            const width = 600, height = 500;
            const left = (screen.width - width) / 2;
            const top = (screen.height - height) / 2;

            window.open(url, 'ShareWindow', `width=${width},height=${height},top=${top},left=${left}`);
        });
    });
});
