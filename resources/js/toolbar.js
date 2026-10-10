(() => {
    const toolbar = document.currentScript.previousElementSibling;
    const toggle = toolbar.querySelector('.sst-toggle');
    const menu = toolbar.querySelector('.sst-menu');
    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Collapse toolbar' : 'Expand toolbar');
        menu.hidden = !open;
    };

    try {
        setOpen(localStorage.getItem('statamic-toolbar-open') === 'true');
    } catch {}

    toggle.addEventListener('click', () => {
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        setOpen(open);

        try {
            localStorage.setItem('statamic-toolbar-open', String(open));
        } catch {}
    });
})();
