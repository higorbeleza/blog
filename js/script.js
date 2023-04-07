let navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#menu-btn').onclick = () => {
    navbar.classList.toggle('active');
    profile.classList.remove('active');
    search.classList.remove('active');
}

let profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () => {
    profile.classList.toggle('active');
    navbar.classList.remove('active');
    search.classList.remove('active');
}

let search = document.querySelector('.header .flex .search-form');

document.querySelector('#search-btn').onclick = () => {
    search.classList.toggle('active');
    navbar.classList.remove('active');
    profile.classList.remove('active');
}

window.onscroll = () => {
    navbar.classList.remove('active');
    profile.classList.remove('active');
    search.classList.remove('active');
}

document.querySelectorAll('.posts-grid .box-container .box .content').forEach(content => {
    if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0,100);
});