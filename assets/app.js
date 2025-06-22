import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('turbo:load', () => {

const region = document.querySelectorAll('.geo-map g path');
region.forEach((el) => {
    el.addEventListener('click', () => {
        const regionId = el.getAttribute('id');
        const regionTitle = el.getAttribute('title');
        console.log(`Clicked on region: ${regionTitle}`);
        location.href = `/region/${regionTitle}`;
        // Here you can add more logic to handle the click event
    });

})
});
