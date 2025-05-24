import MmenuLight from '../mmenu-light/mmenu-light';

export function initMobilMenu() {
  var menu = new MmenuLight(document.querySelector("#mobil_mmenu"), "all");

  var navigator = menu.navigation({
      title: 'Меню'
  });

  var drawer = menu.offcanvas({
  });

  // Open the menu.
  var menuButton = document.querySelector('a[href="#mobil_mmenu"]');
  if (menuButton) {
      menuButton.addEventListener("click", function(evnt) {
          evnt.preventDefault();
          drawer.open();
      });
  }
}
