(() => {
    'use strict';

    class Lantern {
        constructor() {
            if (!window.LANTERTOWN_CONFIG) {
                return;
            }
            this.initThemeMode();
            this.initLoadMore();
            this.initComment();
            this.initLazyLoad();
        }

        initThemeMode() {
            const blog = document.getElementById('blog_container');
            if (blog) {
                blog.classList.add('bg' + window.LANTERTOWN_CONFIG.THEME_MODE);
            }
        }

        initLoadMore() {
            if (window.LANTERTOWN_CONFIG.TURN_PAGE_TYPE !== 'waterfall') {
                return;
            }
            const loadMoreLink = document.querySelector('.loadmore a');
            if (!loadMoreLink) {
                return;
            }
            loadMoreLink.setAttribute('data-href', loadMoreLink.getAttribute('href') || '');
            loadMoreLink.removeAttribute('href');
            const self = this;
            loadMoreLink.addEventListener('click', async function () {
                if (this.hasAttribute('disabled')) {
                    return;
                }
                this.textContent = 'loading...';
                this.setAttribute('disabled', 'disabled');
                const url = this.getAttribute('data-href');
                if (!url) {
                    this.removeAttribute('disabled');
                    this.textContent = '加载更多';
                    return;
                }
                try {
                    const response = await fetch(url, { credentials: 'same-origin' });
                    const data = await response.text();
                    this.removeAttribute('disabled');
                    this.textContent = '加载更多';
                    const parser = new DOMParser();
                    const parsed = parser.parseFromString(data, 'text/html');
                    const list = parsed.querySelectorAll('.recent');
                    const articleList = document.getElementById('articleList');
                    let firstAdded = null;
                    if (articleList && list.length) {
                        list.forEach((node) => {
                            const imported = document.importNode(node, true);
                            if (!firstAdded) {
                                firstAdded = imported;
                            }
                            articleList.appendChild(imported);
                        });
                    }
                    const navbar = document.querySelector('.navbar');
                    if (firstAdded) {
                        window.scroll({
                            top: firstAdded.getBoundingClientRect().top + window.pageYOffset - ((navbar ? navbar.offsetHeight : 0) + 20),
                            behavior: 'smooth'
                        });
                    }
                    const newURL = parsed.querySelector('.loadmore a')?.getAttribute('href') || '';
                    if (newURL) {
                        this.setAttribute('data-href', newURL);
                    } else {
                        this.closest('.loadmore')?.remove();
                    }
                    self.initLazyLoad();
                } catch {
                    this.removeAttribute('disabled');
                    this.textContent = '加载更多';
                }
            });
        }

        initComment() {
            window.TypechoComment = {
                dom: function (id) {
                    return document.getElementById(id);
                },
                create: function (tag, attr) {
                    const el = document.createElement(tag);
                    for (const key in attr) {
                        if (Object.prototype.hasOwnProperty.call(attr, key)) {
                            el.setAttribute(key, attr[key]);
                        }
                    }
                    return el;
                },
                reply: function (cid, coid) {
                    const comment = this.dom(cid);
                    const respond = document.querySelector('.respond');
                    const response = this.dom(respond ? respond.getAttribute('data-respondId') : '');
                    let input = this.dom('comment-parent');
                    const form = response && response.tagName === 'FORM' ? response : (response ? response.getElementsByTagName('form')[0] : null);
                    if (!comment || !response || !form) {
                        return false;
                    }
                    const textarea = response.getElementsByTagName('textarea')[0];
                    if (input === null) {
                        input = this.create('input', {
                            type: 'hidden',
                            name: 'parent',
                            id: 'comment-parent'
                        });
                        form.appendChild(input);
                    }
                    input.setAttribute('value', coid);
                    if (this.dom('comment-form-place-holder') === null) {
                        const holder = this.create('div', {
                            id: 'comment-form-place-holder'
                        });
                        response.parentNode.insertBefore(holder, response);
                    }
                    if (!comment.contains(response)) {
                        comment.appendChild(response);
                    }
                    this.dom('cancel-comment-reply-link').style.display = '';
                    if (textarea !== null && textarea.name === 'text') {
                        const nav = document.querySelector('.navbar');
                        const anchor = this.dom(cid);
                        const comments = this.dom('comments');
                        window.scroll({
                            top: ((anchor ? anchor.getBoundingClientRect().top + window.pageYOffset : (comments ? comments.getBoundingClientRect().top + window.pageYOffset : 0))) - ((nav ? nav.offsetHeight : 0) + 20),
                            behavior: 'smooth'
                        });
                    }
                    return false;
                },
                cancelReply: function () {
                    const respond = document.querySelector('.respond');
                    const response = this.dom(respond ? respond.getAttribute('data-respondId') : '');
                    const holder = this.dom('comment-form-place-holder');
                    const input = this.dom('comment-parent');
                    if (!response) {
                        return true;
                    }
                    if (input !== null) {
                        input.parentNode.removeChild(input);
                    }
                    if (holder === null) {
                        return true;
                    }
                    this.dom('cancel-comment-reply-link').style.display = 'none';
                    holder.parentNode.insertBefore(response, holder);
                    const comments = this.dom('comments');
                    const nav = document.querySelector('.navbar');
                    window.scroll({
                        top: (comments ? comments.getBoundingClientRect().top + window.pageYOffset : 0) - ((nav ? nav.offsetHeight : 0) + 20),
                        behavior: 'smooth'
                    });
                    return false;
                }
            };
        }

        initLazyLoad() {
            const lazyBgElements = document.querySelectorAll('.lazy-bg[data-src]');
            if (!lazyBgElements.length) {
                return;
            }

            const loadImage = (el) => {
                const src = el.getAttribute('data-src');
                if (src) {
                    el.style.backgroundImage = `url('${src}')`;
                    el.classList.add('loaded');
                    el.removeAttribute('data-src');
                    el.classList.remove('lazy-bg');
                }
            };

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            loadImage(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    rootMargin: '50px 0px',
                    threshold: 0.01
                });

                lazyBgElements.forEach(el => observer.observe(el));
            } else {
                lazyBgElements.forEach(el => loadImage(el));
            }
        }
    }

    window.Lantern = Lantern;
    new Lantern();
})();
