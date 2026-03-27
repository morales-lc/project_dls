document.addEventListener('DOMContentLoaded', function() {
    function decodeHtmlEntities(input) {
        var parser = document.createElement('textarea');
        parser.innerHTML = input;
        return parser.value;
    }

    // Handle alert book card clicks to view PDF
    document.querySelectorAll('.alert-book-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Don't open PDF if clicking on buttons or forms
            if (e.target.closest('button') || e.target.closest('form') || e.target.closest('a')) {
                return;
            }

            // First click reveals card actions; second click opens PDF.
            if (!this.classList.contains('is-active')) {
                document.querySelectorAll('.alert-book-card.is-active').forEach(function(activeCard) {
                    activeCard.classList.remove('is-active');
                });
                this.classList.add('is-active');
                return;
            }

            var pdfUrl = this.getAttribute('data-pdf-url');
            if (pdfUrl) {
                window.open(pdfUrl, '_blank');
            } else {
                alert('No PDF available for this book.');
            }
        });
    });

    // Clicking outside closes active alert-book action states.
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.alert-book-card')) {
            document.querySelectorAll('.alert-book-card.is-active').forEach(function(activeCard) {
                activeCard.classList.remove('is-active');
            });
        }
    });

    // Handle bookmark toggle for alert books
    document.querySelectorAll('.bookmark-toggle-alert-dashboard').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var btn = form.querySelector('button');
            if (!btn) return;
            var label = btn.querySelector('.label');
            var icon = btn.querySelector('i');
            var originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing';

            var formData = new FormData(form);
            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            var csrfValue = csrfToken ? csrfToken.getAttribute('content') : '';
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfValue
                },
                body: formData
            }).then(function(res) {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            }).then(function(data) {
                if (data && (data.status === 'removed' || data.status === 'bookmarked')) {
                    var bookmarked = data.status === 'bookmarked';
                    var iconClass = bookmarked ? 'bi-bookmark-fill' : 'bi-bookmark';
                    var labelText = bookmarked ? 'Bookmarked' : 'Bookmark';
                    var btnClass = bookmarked ? 'btn-success' : 'btn-outline-light';
                    
                    btn.className = 'btn btn-sm w-100 ' + btnClass;
                    btn.innerHTML = '<i class="bi ' + iconClass + '"></i> <span class="label">' + labelText + '</span>';
                    btn.disabled = false;
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            }).catch(function(err) {
                console.error('Error:', err);
                alert(err && err.message ? err.message : 'Failed to update bookmark.');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
        });
    });

    // Initialize alert books carousel manually
    var alertCarousel = document.getElementById('alert-books-carousel');
    if (alertCarousel) {
        var wrap = alertCarousel.closest('.news-carousel-wrap');
        var leftBtn = wrap.querySelector('.carousel-btn.left');
        var rightBtn = wrap.querySelector('.carousel-btn.right');
        var dotsWrap = document.getElementById('alert-books-carousel-dots');
        var cards = alertCarousel.querySelectorAll('.carousel-card');
        var visibleCards = 3;
        var cardWidth = 0;
        var gap = 0;
        var total = cards.length;
        var pos = 0;

        function recalcCardWidth() {
            if (window.innerWidth < 992) {
                cardWidth = alertCarousel.querySelector('.carousel-card')?.offsetWidth || 220;
                visibleCards = 1;
                gap = 0;
            } else {
                cardWidth = alertCarousel.querySelector('.carousel-card')?.offsetWidth || 220;
                visibleCards = 3;
                if (cards.length > 1) {
                    var style = window.getComputedStyle(cards[1]);
                    gap = parseFloat(style.marginLeft || 0);
                } else {
                    gap = 0;
                }
            }
        }

        function getDotCount() {
            return Math.max(1, total - visibleCards + 1);
        }

        function renderDots() {
            var dotCount = getDotCount();
            dotsWrap.innerHTML = '';
            for (let i = 0; i < dotCount; i++) {
                var dot = document.createElement('span');
                dot.className = 'carousel-dot' + (i === pos ? ' active' : '');
                dot.setAttribute('tabindex', '0');
                dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                dot.addEventListener('click', function() {
                    scrollToIdx(i);
                });
                dot.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        scrollToIdx(i);
                    }
                });
                dotsWrap.appendChild(dot);
            }
        }

        function updateDots() {
            var dots = dotsWrap.querySelectorAll('.carousel-dot');
            dots.forEach(function(dot, i) {
                dot.classList.toggle('active', i === pos);
            });
        }

        function scrollToIdx(idx) {
            pos = idx;
            var scrollAmount = (cardWidth + gap) * pos;
            alertCarousel.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
            updateDots();
            updateBtns();
        }

        function updateBtns() {
            var dotCount = getDotCount();
            leftBtn.disabled = pos === 0;
            rightBtn.disabled = pos >= dotCount - 1;
        }
        
        leftBtn.addEventListener('click', function() {
            if (pos > 0) scrollToIdx(pos - 1);
        });
        
        rightBtn.addEventListener('click', function() {
            if (pos < getDotCount() - 1) scrollToIdx(pos + 1);
        });
        
        window.addEventListener('resize', function() {
            recalcCardWidth();
            renderDots();
            updateDots();
            updateBtns();
            scrollToIdx(pos);
        });
        
        recalcCardWidth();
        renderDots();
        scrollToIdx(0);
    }

    // Modal logic
    var postModal = new bootstrap.Modal(document.getElementById('postModal'));
    document.querySelectorAll('.card-clickable').forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Ignore clicks on bookmark buttons or inside forms/links
            if (e.target.closest('.post-bookmark-toggle') ||
                e.target.closest('.post-bookmark-btn') ||
                e.target.closest('form') ||
                e.target.closest('a')) {
                e.stopPropagation();
                return;
            }

            var title = card.getAttribute('data-title') || '';
            var type = card.getAttribute('data-type') || '';
            var descContainer = card.querySelector('.post-description-html');
            var desc = descContainer ? descContainer.innerHTML : (card.getAttribute('data-description') || '');
            if (desc && desc.indexOf('&lt;') !== -1) {
                desc = decodeHtmlEntities(desc);
            }
            var photo = card.getAttribute('data-photo') || '';
            var youtube = card.getAttribute('data-youtube') || '';
            var website = card.getAttribute('data-website') || '';
            var ogthumb = card.getAttribute('data-ogthumb') || '';
            var favicon = card.getAttribute('data-favicon') || '';
            var imageHtml = '';
            var placeholder = card.getAttribute('data-placeholder') || '';

            if (photo) {
                imageHtml = '<img src="' + photo + '" alt="Photo"/>';
            } else if (youtube) {
                var match = youtube.match(/v=([^&]+)/);
                var ytid = match ? match[1] : null;
                if (ytid) imageHtml = '<iframe src="https://www.youtube.com/embed/' + ytid + '" title="YouTube video" allowfullscreen style="width:100%;height:340px;border:none;"></iframe>';
            } else if (website) {
                imageHtml = '<img src="' + (ogthumb || favicon) + '" alt="Website Thumbnail"/>';
            } else {
                imageHtml = '<img src="' + placeholder + '" alt="No Image"/>';
            }

            document.getElementById('postModalLabel').textContent = title;
            document.getElementById('postModalType').textContent = type;
            document.getElementById('postModalDesc').innerHTML = desc || '<p class="mb-0">No description provided.</p>';
            document.getElementById('postModalImageWrap').innerHTML = imageHtml;

            var linksHtml = '';
            if (website) linksHtml += '<a href="' + website + '" target="_blank" class="btn btn-primary">Visit Website</a>';
            if (youtube) linksHtml += '<a href="' + youtube + '" target="_blank" class="btn btn-danger">Watch Video</a>';
            document.getElementById('postModalLinks').innerHTML = linksHtml;

            postModal.show();
        });
    });

    // Post bookmark toggles
    document.querySelectorAll('.post-bookmark-toggle').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var btn = form.querySelector('.post-bookmark-btn');
            var original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>...';

            var fd = new FormData(form);
            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            var csrfValue = csrfToken ? csrfToken.getAttribute('content') : '';
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfValue
                },
                body: fd
            }).then(function(res) {
                console.log('Response status:', res.status, res.statusText);
                if (!res.ok) {
                    return res.text().then(function(text) {
                        console.error('Error response:', text);
                        throw new Error('Server returned error: ' + res.status);
                    });
                }
                return res.text().then(function(text) {
                    console.log('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Failed to parse JSON:', e);
                        console.error('Response was:', text);
                        throw new Error('Invalid JSON response');
                    }
                });
            }).then(function(data) {
                console.log('Parsed data:', data);
                if (data && (data.status === 'removed' || data.status === 'bookmarked')) {
                    var bookmarked = data.status === 'bookmarked';
                    
                    // Reconstruct button HTML with proper state
                    var iconClass = bookmarked ? 'bi-bookmark-fill' : 'bi-bookmark';
                    var labelText = bookmarked ? 'Bookmarked' : 'Bookmark';
                    var btnClass = bookmarked ? 'btn-primary' : 'btn-outline-secondary';
                    
                    btn.className = 'btn btn-sm post-bookmark-btn ' + btnClass;
                    btn.innerHTML = '<i class="bi ' + iconClass + ' me-1"></i><span>' + labelText + '</span>';
                    btn.disabled = false;
                } else {
                    console.error('Unexpected data structure:', data);
                    btn.disabled = false;
                    btn.innerHTML = original;
                }
            }).catch(function(err) {
                console.error('Full error details:', err);
                alert('Failed to toggle bookmark. Check console for details.');
                btn.disabled = false;
                btn.innerHTML = original;
            });
        });
    });

    // Carousel logic for each section
    document.querySelectorAll('.news-carousel').forEach(function(carousel, idx) {
        var wrap = carousel.closest('.news-carousel-wrap');
        var leftBtn = wrap.querySelector('.carousel-btn.left');
        var rightBtn = wrap.querySelector('.carousel-btn.right');
        var dotsWrap = document.getElementById('carousel-dots-' + idx);
        var cards = carousel.querySelectorAll('.carousel-card');
        var visibleCards = 3;
        var cardWidth = 0;
        var gap = 0;
        var total = cards.length;
        var pos = 0;

        function recalcCardWidth() {
            if (window.innerWidth < 992) {
                cardWidth = carousel.querySelector('.carousel-card')?.offsetWidth || 320;
                visibleCards = 1;
                gap = 0;
            } else {
                cardWidth = carousel.querySelector('.carousel-card')?.offsetWidth || 320;
                visibleCards = 3;
                // Get computed gap between cards
                if (cards.length > 1) {
                    var style = window.getComputedStyle(cards[1]);
                    gap = parseFloat(style.marginLeft || 0);
                } else {
                    gap = 0;
                }
            }
        }

        function getDotCount() {
            return Math.max(1, total - visibleCards + 1);
        }

        function renderDots() {
            var dotCount = getDotCount();
            dotsWrap.innerHTML = '';
            for (let i = 0; i < dotCount; i++) {
                var dot = document.createElement('span');
                dot.className = 'carousel-dot' + (i === pos ? ' active' : '');
                dot.setAttribute('tabindex', '0');
                dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                dot.addEventListener('click', function() {
                    scrollToIdx(i);
                });
                dot.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        scrollToIdx(i);
                    }
                });
                dotsWrap.appendChild(dot);
            }
        }

        function updateDots() {
            var dots = dotsWrap.querySelectorAll('.carousel-dot');
            dots.forEach(function(dot, i) {
                dot.classList.toggle('active', i === pos);
            });
        }

        function scrollToIdx(idx) {
            pos = idx;
            var scrollAmount = (cardWidth + gap) * pos;
            carousel.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
            updateDots();
            updateBtns();
        }

        function updateBtns() {
            var dotCount = getDotCount();
            leftBtn.disabled = pos === 0;
            rightBtn.disabled = pos >= dotCount - 1;
        }
        
        leftBtn.addEventListener('click', function() {
            if (pos > 0) scrollToIdx(pos - 1);
        });
        
        rightBtn.addEventListener('click', function() {
            if (pos < getDotCount() - 1) scrollToIdx(pos + 1);
        });
        
        // Responsive: recalc on resize
        window.addEventListener('resize', function() {
            recalcCardWidth();
            renderDots();
            updateDots();
            updateBtns();
            scrollToIdx(pos);
        });
        
        // Init
        recalcCardWidth();
        renderDots();
        scrollToIdx(0);
    });
});
