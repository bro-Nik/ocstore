class Menu {
  constructor() {
    this.menu = document.getElementById('menu2');
    this.activeRow = null;
    this.mouseLocs = [];
    this.rowSelector = ".revlevel_1";
    this.exitTimeout = null;

    this.bindEvents();
  }

  activateRow(row) {
    this.enterMenu(row);
    
    const childBox = row.querySelector('.child-box');
    if (childBox) childBox.style.minHeight = this.menu.offsetHeight + 'px';
  }

  bindEvents() {
    this.menu.addEventListener('mouseleave', this.mouseleaveMenu.bind(this));
    
    const rows = this.menu.querySelectorAll(this.rowSelector);
    rows.forEach(row => {
      row.addEventListener('mouseenter', this.mouseenterRow.bind(this));
    });

    document.addEventListener('mousemove', this.mousemoveDocument.bind(this));
  }

  mousemoveDocument(e) {
    this.mouseLocs.push({x: e.pageX, y: e.pageY});
    if (this.mouseLocs.length > 3) this.mouseLocs.shift();
  }

  mouseleaveMenu(e) {
    if (this.activeRow) this.deactivate(this.activeRow);
    this.activeRow = null;
  }

  mouseenterRow(e) {
    this.enterMenu(e.currentTarget);
    this.activate(e.currentTarget);
  }

  deactivate(row) {
    this.toggleClassesElement(row, 'open', 'closed');
  }

  activate(row) {
    if (row === this.activeRow) return;
    if (this.activeRow) this.deactivate(this.activeRow);

    this.activateRow(row);
    this.activeRow = row;
  }

  enterMenu(row) {
    this.toggleClasses('.catalog_list .revlevel_1 div.open', 'open', 'closed');
    this.toggleClassesElement(row, 'closed', 'open');
  }

  toggleClasses(selector, rmCls, addCls) {
    this.menu.querySelectorAll(selector)?.forEach(el => {
      this.toggleClassesElement(el, rmCls, addCls);
    });
  }

  toggleClassesElement(e, rmCls, addCls) {
    e.classList.remove(rmCls);
    e.classList.add(addCls);
  }
}

export const menu = new Menu();
