window.faviconManager = {
    set(src) {
        let link = document.querySelector("link[rel~='icon']");
        if (!link) {
            link = document.createElement("link");
            link.rel = "icon";
            document.head.appendChild(link);
        }
        link.href = src;
    },
    flash(src, duration = 1500) {
        const original = document.querySelector("link[rel~='icon']")?.href;
        this.set(src);
        if (original) {
            setTimeout(() => this.set(original), duration);
        }
    }
};
