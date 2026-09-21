(function () {
    'use strict';
    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    function byId(id) {
        return document.getElementById(id);
    }

    function initSearch() {
        const form = document.querySelector('.search-form');
        const input = byId('search-input');
        const clearBtn = byId('clear-search-btn');
        const box = byId('search-suggestions');
        const wrapper = document.querySelector('.search-input-wrapper');

        if (!form || !input) return;
        const apiLink = document.querySelector('link[rel="https://api.w.org/"]');
        const apiRoot = apiLink ? apiLink.href : '/wp-json/';

        let timer = null;
        let controller = null;

        function hideSuggestions() {
            if (controller) controller.abort();
            if (box) {
                box.hidden = true;
                box.textContent = '';
            }
        }

        function toggleClear() {
            const empty = input.value.trim() === '';
            if (clearBtn) clearBtn.hidden = empty;
            if (empty) hideSuggestions();
        }

        // পোস্টের টাইটেলে &#8211; এর মতো HTML entity থাকে; এটাকে সাধারণ টেক্সটে বদলানো
        function decodeHtml(html) {
            return new DOMParser().parseFromString(html, 'text/html').body.textContent;
        }

        function showSuggestions(posts) {
            if (!box) return;
            box.textContent = '';

            posts.forEach(function (post) {
                const link = document.createElement('a');
                link.className = 'suggestion-item';
                link.href = post.link;

                const icon = document.createElement('span');
                icon.className = 'material-icons';
                icon.setAttribute('aria-hidden', 'true');
                icon.textContent = 'search';

                // textContent ব্যবহার করায় টাইটেলের ভেতরের কোনো HTML/স্ক্রিপ্ট চলবে না
                const label = document.createElement('span');
                label.textContent = decodeHtml(post.title.rendered);

                link.append(icon, label);
                box.appendChild(link);
            });

            box.hidden = posts.length === 0;
        }

        function fetchSuggestions(query) {
            if (controller) controller.abort(); // আগের অসমাপ্ত রিকোয়েস্ট বাতিল
            controller = new AbortController();

            const url = new URL(apiRoot + 'wp/v2/posts', window.location.href);
            url.searchParams.set('search', query);
            url.searchParams.set('per_page', '5');
            url.searchParams.set('_fields', 'id,link,title');

            fetch(url.toString(), { signal: controller.signal })
                .then(function (response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(function (posts) {
                    // ততক্ষণে ইউজার অন্য কিছু লিখে ফেললে পুরনো ফলাফল দেখাবে না
                    if (input.value.trim() === query) showSuggestions(posts);
                })
                .catch(function (err) {
                    if (err.name !== 'AbortError') hideSuggestions();
                });
        }

        // টাইপ করার সময়: প্রতি অক্ষরে নয়, থামার ২৫০ms পরে সার্চ করবে
        input.addEventListener('input', function () {
            toggleClear();
            const query = input.value.trim();
            clearTimeout(timer);

            if (query.length < 2) {
                hideSuggestions();
                return;
            }
            timer = setTimeout(function () {
                fetchSuggestions(query);
            }, 250);
        });

        // Clear (X) বাটন
        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.preventDefault();
                input.value = '';
                toggleClear();
                input.focus();
            });
        }

        // বাইরে ক্লিক বা Esc চাপলে সাজেশন বন্ধ
        document.addEventListener('click', function (e) {
            if (wrapper && !wrapper.contains(e.target)) hideSuggestions();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') hideSuggestions();
        });

        // বক্স খালি থাকলে সাবমিট হবে না
        form.addEventListener('submit', function (e) {
            if (input.value.trim() === '') {
                e.preventDefault();
                input.focus();
            }
        });

        toggleClear(); // পেজ লোডের সময়ই অবস্থা ঠিক করা
    }

    /* =========================================================
       ২. মাইক (ভয়েস সার্চ)
       ========================================================= */
    function initMic() {
        const micBtn = byId('mic-btn');
        const input = byId('search-input');
        const form = document.querySelector('.search-form');

        if (!micBtn || !input) return;

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        // ব্রাউজার সাপোর্ট না করলে (যেমন Firefox) বাটন লুকিয়ে ফেলা
        if (!SpeechRecognition) {
            micBtn.hidden = true;
            return;
        }

        const MIC_LANG = 'bn-BD'; // ইংরেজি চাইলে 'en-US' দিন
        const recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = MIC_LANG;

        function stopListening() {
            micBtn.classList.remove('listening');
            micBtn.setAttribute('aria-pressed', 'false');
            input.placeholder = 'Search';
        }

        micBtn.addEventListener('click', function () {
            if (micBtn.classList.contains('listening')) {
                recognition.stop();
                return;
            }
            try {
                recognition.start();
            } catch (err) {
                stopListening(); // আগের সেশন তখনও চলছিল
            }
        });

        recognition.onstart = function () {
            micBtn.classList.add('listening');
            micBtn.setAttribute('aria-pressed', 'true');
            input.placeholder = 'Listening...';
        };

        recognition.onresult = function (event) {
            input.value = event.results[0][0].transcript;
            if (form) {
                if (form.requestSubmit) {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }
        };

        recognition.onerror = stopListening;
        recognition.onend = stopListening;
    }

    /* =========================================================
       ৩. ড্রপডাউন (Create, Notification, Profile)
       একটাই কোড সবগুলোর জন্য: একটা খুললে বাকিগুলো বন্ধ হয়।
       ========================================================= */
    function initDropdowns() {
        const pairs = [
            ['create-btn', 'create-menu'],
            ['notification-btn', 'notification-menu'],
            ['profile-avatar-btn', 'profile-menu']
        ];

        const items = pairs
            .map(function (pair) {
                return { btn: byId(pair[0]), menu: byId(pair[1]) };
            })
            .filter(function (item) {
                return item.btn && item.menu;
            });

        if (items.length === 0) return;

        function setOpen(item, open) {
            item.menu.hidden = !open;
            item.btn.setAttribute('aria-expanded', String(open));
        }

        items.forEach(function (item) {
            item.btn.addEventListener('click', function () {
                const willOpen = item.menu.hidden;
                items.forEach(function (other) {
                    setOpen(other, other === item ? willOpen : false);
                });
            });
        });

        // বাইরে ক্লিক করলে সব বন্ধ
        document.addEventListener('click', function (e) {
            items.forEach(function (item) {
                if (!item.btn.parentElement.contains(e.target)) setOpen(item, false);
            });
        });

        // Esc চাপলে বন্ধ হবে এবং ফোকাস বাটনে ফিরে যাবে
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            items.forEach(function (item) {
                if (!item.menu.hidden) {
                    setOpen(item, false);
                    item.btn.focus();
                }
            });
        });
    }

    /* =========================================================
       ৪. বাম পাশের ফুল ড্রয়ার
       ========================================================= */
    function initDrawer() {
        const menuBtn = byId('menu-btn');
        const closeBtn = byId('drawer-close-btn');
        const drawer = byId('full-drawer');
        const overlay = byId('drawer-overlay');

        if (!menuBtn || !drawer || !overlay) return;

        function setDrawer(open) {
            drawer.classList.toggle('open', open);
            overlay.classList.toggle('active', open);
            document.body.classList.toggle('drawer-open', open); // পেছনের পেজ স্ক্রল বন্ধ
            menuBtn.setAttribute('aria-expanded', String(open));

            if (open && closeBtn) {
                closeBtn.focus();
            } else if (!open) {
                menuBtn.focus();
            }
        }

        menuBtn.addEventListener('click', function () {
            setDrawer(true);
        });
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                setDrawer(false);
            });
        }
        overlay.addEventListener('click', function () {
            setDrawer(false);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && drawer.classList.contains('open')) {
                setDrawer(false);
            }
        });
    }

    ready(function () {
        initSearch();
        initMic();
        initDropdowns();
        initDrawer();
    });
})();