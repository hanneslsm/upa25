///
/// Header fixed
///
document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('header .is-style-header-fixed');
  if (!header) return;

  let lastScroll = 0;

  function adjustContentOffset() {
    const h = header.getBoundingClientRect().height;
    document.body.style.paddingTop = h + 'px';
    return h;
  }

  let headerHeight = adjustContentOffset();

  window.addEventListener('resize', () => {
    headerHeight = adjustContentOffset();
  });

  const isNavOpen = () =>
    Boolean(
      document.querySelector(
        '.wp-block-navigation__responsive-container.is-menu-open,' +
        '.wp-block-navigation.is-menu-open'
      )
    );

  window.addEventListener('scroll', () => {
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;

    if (isNavOpen()) {
      header.style.transform = 'translateY(0)';
    } else if (scrollY > lastScroll && scrollY > headerHeight) {
      header.style.transform = 'translateY(-100%)';
    } else {
      header.style.transform = 'translateY(0)';
    }

    lastScroll = scrollY;
  });
});
