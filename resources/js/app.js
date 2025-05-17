'use strict';

const url = 'teste';

let btnOpen = document.querySelector('#btn-open');
let btnClose = document.querySelector('#btn-close');
let itemsMobile = document.querySelector('.items-menu-mobile');

btnOpen.addEventListener('click', () => {
    itemsMobile.style.display = 'flex';
});

btnClose.addEventListener('click', () => {
    itemsMobile.style.display = 'none';
});