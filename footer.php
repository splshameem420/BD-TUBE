<?php wp_footer(); ?>

<script>
function updateRealtimeViews() {
    const viewElements = document.querySelectorAll('.realtime-views');
    
    viewElements.forEach(function(elem) {
        const postId = elem.getAttribute('data-post-id');
        
        if (postId) {
            fetch('<?php echo get_template_directory_uri(); ?>/get-views.php?id=' + postId)
                .then(response => response.text()) // প্রথমে টেক্সট হিসেবে চেক করা
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data && data.views) {
                            elem.innerText = data.views;
                        }
                    } catch (e) {
                        console.error('PHP Output is not valid JSON:', text);
                    }
                })
                .catch(error => console.error('Fetch error:', error));
        }
    });
}

setInterval(updateRealtimeViews, 5000);
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('toggle-sub-channels-btn');
    if (!toggleBtn) return;

    var hiddenItems = document.querySelectorAll('.sub-hidden-item');
    var icon = toggleBtn.querySelector('.toggle-icon');
    var text = toggleBtn.querySelector('.toggle-text');
    var channelsUrl = toggleBtn.getAttribute('data-channels-url');

    // ১. পেজ লোড হওয়ার সময় লোকাল স্টোরেজ চেক করা (ইউজার Expanded মোডে আছে কি না)
    var subState = localStorage.getItem('bdtube_sub_drawer_state');

    if (subState === 'expanded') {
        expandDrawer();
    } else {
        collapseDrawer();
    }

    // ২. বাটনে ক্লিক ইভেন্ট
    toggleBtn.addEventListener('click', function(e) {
        var isExpanded = toggleBtn.classList.contains('expanded');

        if (!isExpanded) {
            // Show More-এ ক্লিক করলে স্টেট Expanded হবে এবং /feed/subscriptions/channels/ এ রিডাইরেক্ট করবে
            localStorage.setItem('bdtube_sub_drawer_state', 'expanded');
            window.location.href = channelsUrl;
        } else {
            // Show Less-এ ক্লিক করলে রিসেট হয়ে সংকুচিত মোডে ফিরবে
            localStorage.setItem('bdtube_sub_drawer_state', 'collapsed');
            collapseDrawer();
        }
    });

    // ড্রয়ার প্রসারিত করার ফাংশন
    function expandDrawer() {
        hiddenItems.forEach(function(item) {
            item.style.display = 'flex';
        });
        text.innerText = 'Show less';
        icon.innerText = 'expand_less';
        toggleBtn.classList.add('expanded');
    }

    // ড্রয়ার রিসেট / সংকুচিত করার ফাংশন
    function collapseDrawer() {
        hiddenItems.forEach(function(item) {
            item.style.display = 'none';
        });
        text.innerText = 'Show more';
        icon.innerText = 'expand_more';
        toggleBtn.classList.remove('expanded');
    }
});
</script>

</body>
</html>