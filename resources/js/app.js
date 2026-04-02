import './bootstrap';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

document.documentElement.classList.add('js');

const qs = (sel, root = document) => root.querySelector(sel);
const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

function safeInit(name, fn, onError) {
    try {
        fn();
    } catch (err) {
        // eslint-disable-next-line no-console
        console.error(`[app] ${name} failed`, err);
        onError?.(err);
    }
}

function isReducedMotion() {
    return window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches ?? false;
}

function readJsonScript(id) {
    const el = document.getElementById(id);
    if (!(el instanceof HTMLScriptElement)) {
        return null;
    }

    try {
        return JSON.parse(el.textContent ?? 'null');
    } catch {
        return null;
    }
}

function initReveal() {
    if (isReducedMotion()) {
        qsa('[data-reveal]').forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const items = qsa('[data-reveal]');
    if (items.length === 0) {
        return;
    }

    gsap.registerPlugin(ScrollTrigger);

    items.forEach((el) => {
        gsap.fromTo(
            el,
            { opacity: 0, y: 16 },
            {
                opacity: 1,
                y: 0,
                duration: 0.9,
                ease: 'power3.out',
                clearProps: 'transform',
                scrollTrigger: {
                    trigger: el,
                    start: 'top 86%',
                    once: true,
                },
                onStart: () => el.classList.add('is-revealed'),
            },
        );
    });
}

async function copyText(text) {
    const value = String(text ?? '').replaceAll('\\n', '\n');
    if (value === '') {
        return false;
    }

    try {
        await navigator.clipboard.writeText(value);
        return true;
    } catch {
        const el = document.createElement('textarea');
        el.value = value;
        el.setAttribute('readonly', 'true');
        Object.assign(el.style, {
            position: 'fixed',
            left: '0',
            top: '0',
            width: '2em',
            height: '2em',
            padding: '0',
            border: 'none',
            outline: 'none',
            boxShadow: 'none',
            background: 'transparent',
            opacity: '0',
            pointerEvents: 'none',
        });
        document.body.appendChild(el);
        el.focus();
        el.select();
        el.setSelectionRange(0, value.length);
        let ok = false;
        try {
            ok = document.execCommand('copy');
        } finally {
            document.body.removeChild(el);
        }
        return ok;
    }
}

function showCopyToast(message) {
    const existing = document.getElementById('copy-toast');
    if (existing) {
        existing.remove();
    }
    const toast = document.createElement('div');
    toast.id = 'copy-toast';
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');
    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '1.5rem',
        left: '50%',
        transform: 'translateX(-50%)',
        zIndex: '9999',
        padding: '0.5rem 1rem',
        borderRadius: '0.75rem',
        background: 'rgb(24 24 27)',
        color: 'white',
        fontSize: '0.875rem',
        fontWeight: '600',
        boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)',
        transition: 'opacity 0.2s',
    });
    toast.textContent = message;
    document.body.appendChild(toast);
    window.setTimeout(() => {
        toast.style.opacity = '0';
        window.setTimeout(() => toast.remove(), 200);
    }, 1200);
}

function initCopyButtons() {
    let lastCopyAt = 0;
    const handleCopy = async (e, btn) => {
        if (Date.now() - lastCopyAt < 400) {
            return;
        }
        lastCopyAt = Date.now();
        const value = btn.getAttribute('data-copy-text') ?? '';
        const ok = await copyText(value);

        const label = btn.getAttribute('data-copy-label');
        const onSuccess = btn.getAttribute('data-copy-label-success') ?? 'Copied';
        const onFail = btn.getAttribute('data-copy-label-fail') ?? label;

        showCopyToast(ok ? onSuccess : onFail);
        if (label) {
            btn.textContent = ok ? onSuccess : onFail;
            window.setTimeout(() => {
                btn.textContent = label;
            }, 1200);
        }
    };

    const delegateCopy = (e) => {
        const btn = e.target instanceof Element ? e.target.closest('[data-copy-text]') : null;
        if (!(btn instanceof HTMLButtonElement || btn instanceof HTMLElement)) {
            return;
        }
        if (e.type === 'touchstart' || e.type === 'touchend') {
            e.preventDefault();
        }
        handleCopy(e, btn);
    };

    document.addEventListener('click', delegateCopy);
    document.addEventListener('touchstart', delegateCopy, { passive: false });
}

function initCommandPalette() {
    const root = qs('[data-command-palette]');
    if (!root) {
        return;
    }

    const input = qs('[data-command-palette-input]', root);
    const closeOverlay = qs('[data-command-palette-close]', root);
    const triggers = qsa('[data-command-palette-trigger]');
    const commands = qsa('[data-command]', root);

    let open = false;
    let lastActive = null;
    let activeIndex = 0;

    const setOpen = (next) => {
        open = next;
        root.classList.toggle('hidden', !open);
        root.setAttribute('aria-hidden', open ? 'false' : 'true');

        if (open) {
            lastActive = document.activeElement instanceof HTMLElement ? document.activeElement : null;
            activeIndex = 0;
            if (input) {
                input.value = '';
                filter('');
                queueMicrotask(() => input.focus());
            }
        } else if (lastActive) {
            lastActive.focus();
            lastActive = null;
        }
    };

    const visibleCommands = () => commands.filter((btn) => !btn.hidden);

    const setActive = (idx) => {
        const list = visibleCommands();
        if (list.length === 0) {
            return;
        }
        activeIndex = Math.max(0, Math.min(idx, list.length - 1));
        list.forEach((el, i) => el.classList.toggle('ring-2', i === activeIndex));
        list.forEach((el, i) => el.classList.toggle('ring-cyan-400/60', i === activeIndex));
        list.forEach((el, i) => el.classList.toggle('dark:ring-cyan-300/30', i === activeIndex));
        list[activeIndex].scrollIntoView({ block: 'nearest' });
    };

    const filter = (q) => {
        const query = String(q ?? '').trim().toLowerCase();
        commands.forEach((btn) => {
            const label = (btn.getAttribute('data-command-label') ?? btn.textContent ?? '').toLowerCase();
            btn.hidden = query !== '' && !label.includes(query);
            btn.classList.remove('ring-2', 'ring-cyan-400/60', 'dark:ring-cyan-300/30');
        });
        setActive(0);
    };

    const run = async (btn) => {
        const href =
            btn.getAttribute('data-command-href') ??
            (btn instanceof HTMLAnchorElement ? btn.getAttribute('href') : null);
        const copy = btn.getAttribute('data-command-copy');
        const copyCurrentUrl = btn.getAttribute('data-command-copy-current-url');

        if (href) {
            setOpen(false);
            window.location.href = href;
            return;
        }

        if (copyCurrentUrl) {
            await copyText(window.location.href);
            setOpen(false);
            return;
        }

        if (copy) {
            await copyText(copy);
            setOpen(false);
        }
    };

    triggers.forEach((t) => t.addEventListener('click', () => setOpen(true)));
    closeOverlay?.addEventListener('click', () => setOpen(false));

    root.addEventListener('click', (e) => {
        const el = e.target instanceof Element ? e.target.closest('[data-command]') : null;
        if (!(el instanceof HTMLElement)) {
            return;
        }

        if (el instanceof HTMLAnchorElement) {
            const isModified =
                e instanceof MouseEvent &&
                (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button === 1);

            if (!isModified) {
                setOpen(false);
            }

            return;
        }

        run(el);
    });

    input?.addEventListener('input', () => filter(input.value));

    root.addEventListener('keydown', (e) => {
        if (!open) {
            return;
        }

        if (e.key === 'Escape') {
            e.preventDefault();
            setOpen(false);
            return;
        }

        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(activeIndex + (e.key === 'ArrowDown' ? 1 : -1));
            return;
        }

        if (e.key === 'Enter') {
            const list = visibleCommands();
            const btn = list[activeIndex];
            if (btn) {
                e.preventDefault();
                run(btn);
            }
            return;
        }

        if (e.key === 'Tab') {
            const focusables = qsa('button,[href],input,[tabindex]:not([tabindex="-1"])', root).filter(
                (el) => !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden') && !el.hidden,
            );
            if (focusables.length === 0) {
                return;
            }
            const first = focusables[0];
            const last = focusables[focusables.length - 1];
            const active = document.activeElement;

            if (!e.shiftKey && active === last) {
                e.preventDefault();
                first.focus();
            } else if (e.shiftKey && active === first) {
                e.preventDefault();
                last.focus();
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        const k = e.key?.toLowerCase?.() ?? '';
        const cmdK = (e.metaKey || e.ctrlKey) && k === 'k';

        if (cmdK) {
            e.preventDefault();
            setOpen(true);
            return;
        }

        if (open && k === 'escape') {
            e.preventDefault();
            setOpen(false);
        }
    });
}

function initHeroTyping() {
    const target = qs('#hero-typing-title');
    if (!(target instanceof HTMLElement)) {
        return;
    }

    const payload = readJsonScript('portfolio-home-data');
    const titles = Array.isArray(payload?.heroTitles) ? payload.heroTitles.filter((t) => typeof t === 'string') : [];
    if (titles.length === 0) {
        return;
    }

    if (isReducedMotion()) {
        target.textContent = titles[0] ?? '';
        return;
    }

    let titleIndex = 0;
    let charIndex = 0;
    let deleting = false;
    let timer = null;

    const step = () => {
        const full = titles[titleIndex] ?? '';

        if (!deleting) {
            charIndex = Math.min(full.length, charIndex + 1);
        } else {
            charIndex = Math.max(0, charIndex - 1);
        }

        target.textContent = full.slice(0, charIndex);

        const isDoneTyping = !deleting && charIndex >= full.length;
        const isDoneDeleting = deleting && charIndex <= 0;

        if (isDoneTyping) {
            deleting = true;
            timer = window.setTimeout(step, 1100);
            return;
        }

        if (isDoneDeleting) {
            deleting = false;
            titleIndex = (titleIndex + 1) % titles.length;
            timer = window.setTimeout(step, 240);
            return;
        }

        timer = window.setTimeout(step, deleting ? 32 : 46);
    };

    step();

    window.addEventListener(
        'beforeunload',
        () => {
            if (timer) {
                window.clearTimeout(timer);
            }
        },
        { once: true },
    );
}

function initHeroCanvas() {
    const canvas = qs('#hero-canvas');
    if (!(canvas instanceof HTMLCanvasElement)) {
        return;
    }

    const ctx = canvas.getContext('2d');
    if (!ctx) {
        return;
    }

    const reduced = isReducedMotion();
    const prefersDark = () => window.matchMedia?.('(prefers-color-scheme: dark)')?.matches ?? false;

    let raf = 0;
    let w = 0;
    let h = 0;
    let dpr = 1;
    let points = [];

    const resize = () => {
        const rect = canvas.getBoundingClientRect();
        dpr = Math.min(2, window.devicePixelRatio || 1);
        w = Math.max(1, Math.floor(rect.width));
        h = Math.max(1, Math.floor(rect.height));
        canvas.width = Math.floor(w * dpr);
        canvas.height = Math.floor(h * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        const count = reduced ? 22 : Math.min(90, Math.floor((w * h) / 18000));
        points = Array.from({ length: count }, () => ({
            x: Math.random() * w,
            y: Math.random() * h,
            vx: (Math.random() - 0.5) * 0.35,
            vy: (Math.random() - 0.5) * 0.35,
        }));
    };

    const tick = () => {
        raf = window.requestAnimationFrame(tick);

        ctx.clearRect(0, 0, w, h);

        const dark = prefersDark();
        const ink = dark ? 'rgba(244,244,245,0.45)' : 'rgba(9,9,11,0.45)';
        const c1 = dark ? 'rgba(217,70,239,0.12)' : 'rgba(217,70,239,0.06)'; // Fuchsia
        const c2 = dark ? 'rgba(14,165,233,0.10)' : 'rgba(14,165,233,0.05)'; // Sky

        const g = ctx.createRadialGradient(w * 0.2, h * 0.2, 0, w * 0.2, h * 0.2, Math.max(w, h) * 0.7);
        g.addColorStop(0, c1);
        g.addColorStop(0.6, 'rgba(0,0,0,0)');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, w, h);

        const g2 = ctx.createRadialGradient(w * 0.8, h * 0.8, 0, w * 0.8, h * 0.8, Math.max(w, h) * 0.7);
        g2.addColorStop(0, c2);
        g2.addColorStop(0.6, 'rgba(0,0,0,0)');
        ctx.fillStyle = g2;
        ctx.fillRect(0, 0, w, h);

        const maxDist = 120;
        for (const p of points) {
            if (!reduced) {
                p.x += p.vx;
                p.y += p.vy;
            }

            if (p.x < -20) p.x = w + 20;
            if (p.x > w + 20) p.x = -20;
            if (p.y < -20) p.y = h + 20;
            if (p.y > h + 20) p.y = -20;
        }

        ctx.lineWidth = 1;
        for (let i = 0; i < points.length; i++) {
            for (let j = i + 1; j < points.length; j++) {
                const a = points[i];
                const b = points[j];
                const dx = a.x - b.x;
                const dy = a.y - b.y;
                const dist = Math.hypot(dx, dy);
                if (dist > maxDist) continue;

                const alpha = (1 - dist / maxDist) * (dark ? 0.20 : 0.12);
                ctx.strokeStyle = dark ? `rgba(217,70,239,${alpha})` : `rgba(14,165,233,${alpha})`;
                ctx.beginPath();
                ctx.moveTo(a.x, a.y);
                ctx.lineTo(b.x, b.y);
                ctx.stroke();
            }
        }

        ctx.fillStyle = ink;
        for (const p of points) {
            ctx.beginPath();
            ctx.arc(p.x, p.y, 1.4, 0, Math.PI * 2);
            ctx.fill();
        }
    };

    resize();
    tick();

    const ro = new ResizeObserver(() => resize());
    ro.observe(canvas);

    window.addEventListener(
        'beforeunload',
        () => {
            window.cancelAnimationFrame(raf);
            ro.disconnect();
        },
        { once: true },
    );
}

function initCodeWindow() {
    const container = qs('#code-typing-container');
    if (!(container instanceof HTMLElement)) {
        return;
    }

    const payload = readJsonScript('portfolio-home-data');
    const lines = Array.isArray(payload?.codeLines) ? payload.codeLines.filter((t) => typeof t === 'string') : [];
    if (lines.length === 0) {
        return;
    }

    const status = qs('#terminal-status-text');
    if (status instanceof HTMLElement) {
        status.textContent = 'System Ready';
    }

    const KEYWORDS = new Set([
        'class',
        'final',
        'readonly',
        'extends',
        'implements',
        'declare',
        'public',
        'private',
        'protected',
        'use',
        'function',
        'return',
        'new',
        'static',
        'array',
        'string',
        'int',
        'bool',
        'true',
        'false',
        'null',
    ]);

    const colorizeLine = (line) => {
        const frag = document.createDocumentFragment();
        const src = String(line ?? '');
        const re =
            /("([^"\\]|\\.)*"|'([^'\\]|\\.)*'|\$[A-Za-z_][A-Za-z0-9_]*|\b[A-Za-z_][A-Za-z0-9_]*\b|->|::|\{|\}|\(|\)|\[|\]|;|:|,)/g;

        let last = 0;
        for (const match of src.matchAll(re)) {
            const idx = match.index ?? 0;
            if (idx > last) {
                frag.appendChild(document.createTextNode(src.slice(last, idx)));
            }

            const token = match[0] ?? '';
            const span = document.createElement('span');

            if (token.startsWith('"') || token.startsWith("'")) {
                span.className = 'text-emerald-300';
            } else if (token.startsWith('$')) {
                span.className = 'text-fuchsia-300';
            } else if (KEYWORDS.has(token)) {
                span.className = 'text-sky-300';
            } else if (token === '->' || token === '::') {
                span.className = 'text-cyan-300';
            } else if ('{}()[];:,.'.includes(token)) {
                span.className = 'text-zinc-100/70';
            } else {
                span.className = 'text-zinc-100/90';
            }

            span.textContent = token;
            frag.appendChild(span);
            last = idx + token.length;
        }

        if (last < src.length) {
            frag.appendChild(document.createTextNode(src.slice(last)));
        }

        return frag;
    };

    const renderLine = (raw) => {
        const line = String(raw ?? '');
        const el = document.createElement('div');
        el.className = 'min-h-[1.2em] whitespace-pre-wrap wrap-break-word';
        el.appendChild(colorizeLine(line));
        return el;
    };

    container.innerHTML = '';

    if (isReducedMotion()) {
        lines.forEach((l) => container.appendChild(renderLine(l)));
        return;
    }

    let i = 0;
    const push = () => {
        const line = lines[i];
        if (typeof line !== 'string') {
            return;
        }

        const el = renderLine(line);
        container.appendChild(el);
        container.scrollTop = container.scrollHeight;

        gsap.fromTo(el, { opacity: 0, y: 8 }, { opacity: 1, y: 0, duration: 0.35, ease: 'power2.out' });

        i++;
        if (i >= lines.length) {
            return;
        }

        window.setTimeout(push, 70);
    };

    window.setTimeout(push, 260);
}

function initModals() {
    const modals = qsa('[data-modal]').filter((el) => el instanceof HTMLElement);
    if (modals.length === 0) {
        return;
    }

    const byName = new Map(modals.map((m) => [m.getAttribute('data-modal'), m]));
    let openName = null;
    let lastActive = null;

    const setOpen = (name, next) => {
        const modal = byName.get(name);
        if (!(modal instanceof HTMLElement)) {
            return;
        }

        const displayClass = modal.getAttribute('data-modal-display');

        if (next) {
            openName = name;
            lastActive = document.activeElement instanceof HTMLElement ? document.activeElement : null;
            modal.classList.remove('hidden');
            if (displayClass) {
                modal.classList.add(displayClass);
            }
            modal.setAttribute('aria-hidden', 'false');

            const focusTarget = qs('[data-modal-close]', modal);
            if (focusTarget instanceof HTMLElement) {
                queueMicrotask(() => focusTarget.focus());
            }
        } else {
            openName = null;
            modal.classList.add('hidden');
            if (displayClass) {
                modal.classList.remove(displayClass);
            }
            modal.setAttribute('aria-hidden', 'true');
            if (lastActive) {
                lastActive.focus();
                lastActive = null;
            }
        }
    };

    document.addEventListener('click', (e) => {
        const t = e.target instanceof Element ? e.target : null;
        if (!t) {
            return;
        }

        const opener = t.closest('[data-modal-open]');
        if (opener) {
            const name = opener.getAttribute('data-modal-open');
            if (name) {
                e.preventDefault();
                setOpen(name, true);
            }
            return;
        }

        const closer = t.closest('[data-modal-close]');
        if (closer) {
            const parentModal = t.closest('[data-modal]');
            const name = parentModal?.getAttribute('data-modal') ?? openName;
            if (name) {
                e.preventDefault();
                setOpen(name, false);
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (!openName) {
            return;
        }

        if (e.key === 'Escape') {
            e.preventDefault();
            setOpen(openName, false);
        }
    });
}

function initTiltCards() {
    if (isReducedMotion()) {
        return;
    }

    const cards = qsa('[data-tilt]').filter((el) => {
        if (!(el instanceof HTMLElement)) {
            return false;
        }

        if (el.hasAttribute('data-tilt-disabled')) {
            return false;
        }

        return true;
    });
    if (cards.length === 0) {
        return;
    }

    cards.forEach((card) => {
        let raf = 0;
        let next = null;
        let rect = null;

        const apply = () => {
            raf = 0;
            if (!next) return;
            const { rx, ry } = next;
            card.style.transform = `perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg) translateZ(0)`;
            card.style.transition = 'transform 140ms ease-out';
        };

        const refreshRect = () => {
            rect = card.getBoundingClientRect();
        };

        const onMove = (e) => {
            if (!rect) {
                refreshRect();
            }
            if (!rect) {
                return;
            }
            const px = (e.clientX - rect.left) / rect.width;
            const py = (e.clientY - rect.top) / rect.height;
            const ry = (px - 0.5) * 8;
            const rx = (0.5 - py) * 6;
            next = { rx, ry };
            if (!raf) {
                raf = window.requestAnimationFrame(apply);
            }
        };

        const reset = () => {
            next = null;
            rect = null;
            if (raf) {
                window.cancelAnimationFrame(raf);
                raf = 0;
            }
            card.style.transform = '';
            card.style.transition = 'transform 260ms ease-out';
        };

        card.addEventListener('mouseenter', refreshRect);
        card.addEventListener('mousemove', onMove);
        card.addEventListener('mouseleave', reset);
        card.addEventListener('blur', reset, true);
    });
}

function initImposterArena() {
    if (isReducedMotion()) {
        return;
    }

    const arenas = qsa('[data-imposter-arena]').filter((el) => el instanceof HTMLElement);
    if (arenas.length === 0) {
        return;
    }

    arenas.forEach((arena) => {
        let rect = null;
        let raf = 0;
        let next = null;

        const refreshRect = () => {
            rect = arena.getBoundingClientRect();
        };

        const apply = () => {
            raf = 0;
            if (!rect || !next) {
                return;
            }

            const x = Math.max(0, Math.min(1, next.x));
            const y = Math.max(0, Math.min(1, next.y));

            arena.style.setProperty('--mx', `${Math.round(x * 100)}%`);
            arena.style.setProperty('--my', `${Math.round(y * 100)}%`);
        };

        const onMove = (e) => {
            if (!rect) {
                refreshRect();
            }
            if (!rect) {
                return;
            }

            next = {
                x: (e.clientX - rect.left) / Math.max(1, rect.width),
                y: (e.clientY - rect.top) / Math.max(1, rect.height),
            };

            if (!raf) {
                raf = window.requestAnimationFrame(apply);
            }
        };

        const onLeave = () => {
            next = null;
            rect = null;
            if (raf) {
                window.cancelAnimationFrame(raf);
                raf = 0;
            }
            arena.style.setProperty('--mx', '50%');
            arena.style.setProperty('--my', '35%');
        };

        const ro = new ResizeObserver(() => refreshRect());
        ro.observe(arena);

        arena.addEventListener('pointerenter', refreshRect, { passive: true });
        arena.addEventListener('pointermove', onMove, { passive: true });
        arena.addEventListener('pointerleave', onLeave, { passive: true });

        window.addEventListener(
            'beforeunload',
            () => {
                ro.disconnect();
                onLeave();
            },
            { once: true },
        );
    });
}

function initMobileNav() {
    const menu = qs('[data-mobile-nav]');
    if (!(menu instanceof HTMLElement)) {
        return;
    }

    let open = false;
    let lastToggleTouchAt = 0;

    const setOpen = (next) => {
        open = next;
        menu.classList.toggle('hidden', !open);
        const t = qs('[data-mobile-nav-toggle]');
        if (t) {
            t.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
    };

    const handleToggle = (e) => {
        const toggle = e.target instanceof Element ? e.target.closest('[data-mobile-nav-toggle]') : null;
        if (!toggle) {
            return;
        }
        if (e.type === 'touchstart') {
            e.preventDefault();
            lastToggleTouchAt = Date.now();
        } else if (e.type === 'click' && Date.now() - lastToggleTouchAt < 500) {
            return;
        }
        setOpen(!open);
    };

    document.addEventListener('touchstart', handleToggle, { passive: false });
    document.addEventListener('click', handleToggle);

    menu.addEventListener('click', (e) => {
        const link = e.target instanceof Element ? e.target.closest('a') : null;
        if (link) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (!open) {
            return;
        }

        if (e.key === 'Escape') {
            e.preventDefault();
            setOpen(false);
        }
    });

    document.addEventListener('click', (e) => {
        if (!open) {
            return;
        }

        const target = e.target instanceof Node ? e.target : null;
        if (!target) {
            return;
        }

        const toggle = qs('[data-mobile-nav-toggle]');
        const clickedToggle = toggle && toggle.contains(target);
        const clickedMenu = menu.contains(target);
        if (!clickedToggle && !clickedMenu) {
            setOpen(false);
        }
    });

    setOpen(false);
}

function initProjectDrawer() {
    const drawer = document.getElementById('project-drawer');
    const backdrop = document.getElementById('drawer-backdrop');
    const surface = document.getElementById('drawer-surface');
    const closeBtn = document.getElementById('close-drawer');
    const body = document.body;

    if (!drawer || !closeBtn) return;

    const setOpen = (open) => {
        if (open) {
            drawer.classList.remove('hidden');
            body.classList.add('overflow-hidden');
            requestAnimationFrame(() => {
                backdrop?.classList.add('opacity-100');
                backdrop?.classList.remove('opacity-0');
                surface?.classList.remove('translate-x-full');
                surface?.classList.add('translate-x-0');
            });
        } else {
            backdrop?.classList.remove('opacity-100');
            backdrop?.classList.add('opacity-0');
            surface?.classList.remove('translate-x-0');
            surface?.classList.add('translate-x-full');

            setTimeout(() => {
                drawer.classList.add('hidden');
                body.classList.remove('overflow-hidden');
            }, 500);
        }
    };

    document.addEventListener('click', (e) => {
        const btn = e.target instanceof Element ? e.target.closest('.js-open-portfolio-drawer') : null;
        if (btn) {
            e.preventDefault();
            setOpen(true);

            // Close command palette if open
            const cp = document.getElementById('command-palette');
            if (cp && !cp.classList.contains('hidden')) {
                const closeCP = cp.querySelector('[data-command-palette-close]');
                if (closeCP instanceof HTMLElement) closeCP.click();
            }
        }
    });

    closeBtn.addEventListener('click', () => setOpen(false));
    backdrop?.addEventListener('click', () => setOpen(false));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !drawer.classList.contains('hidden')) {
            setOpen(false);
        }
    });

    // Check hash on load and change
    const checkHash = () => {
        if (window.location.hash === '#portfolio') {
            setOpen(true);
        }
    };

    checkHash();
    window.addEventListener('hashchange', checkHash);
}

function initHashScroll() {
    const hash = window.location.hash ?? '';
    if (!hash || hash === '#') {
        return;
    }

    const id = decodeURIComponent(hash.slice(1));
    if (!id) {
        return;
    }

    const target = document.getElementById(id);
    if (!(target instanceof HTMLElement)) {
        return;
    }

    const behavior = isReducedMotion() ? 'auto' : 'smooth';
    const scroll = () => target.scrollIntoView({ behavior, block: 'start' });

    window.requestAnimationFrame(() => {
        window.requestAnimationFrame(scroll);
    });

    window.setTimeout(scroll, 180);
    window.setTimeout(scroll, 520);
}

document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('touchstart', () => {}, { passive: true });
    safeInit('reveal', initReveal, () => {
        qsa('[data-reveal]').forEach((el) => el.classList.add('is-revealed'));
        document.documentElement.classList.remove('js');
    });
    safeInit('copyButtons', initCopyButtons);
    safeInit('commandPalette', initCommandPalette);
    safeInit('mobileNav', initMobileNav);
    safeInit('projectDrawer', initProjectDrawer);
    safeInit('modals', initModals);
    safeInit('imposterArena', initImposterArena);
    safeInit('heroCanvas', initHeroCanvas);
    safeInit('heroTyping', initHeroTyping);
    safeInit('codeWindow', initCodeWindow);
    safeInit('hashScroll', initHashScroll);
});
