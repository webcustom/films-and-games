function lazyloadImg(){

    const options = {
       // родитель целевого элемента - область просмотра
       root: null,
       // без отступов
       rootMargin: '0px 0px 0px 0px',
       // процент пересечения - половина изображения
       threshold: 0
    }
 
    // создаем наблюдатель
    const observer = new IntersectionObserver((entries, observer) => {
       // для каждой записи-целевого элемента
       entries.forEach(entry => {
          // если элемент является наблюдаемым
          if (entry.isIntersecting) {
             const lazyImg = entry.target
 
             let dataSrc = lazyImg.getAttribute(['data-src'])
 
              if(lazyImg.tagName === "IMG") {
                  lazyImg.setAttribute('src', dataSrc)
              }else if(lazyImg.tagName === "SOURCE"){
                  lazyImg.setAttribute('srcset', dataSrc)
             }else{
                lazyImg.style.backgroundImage = 'url("'+ dataSrc +'")'
             }
 
             lazyImg.style.opacity = 1
 
             // прекращаем наблюдение
             observer.unobserve(lazyImg)
          }
       })
    }, options)
 
    const arr = document.querySelectorAll('.lazyImg[data-src]') // будет работать с данными элементами
    arr.forEach(i => {
       observer.observe(i)
    })
 
 }
 lazyloadImg();

 
function show_popup(popup_flag) {
    let popup = document.querySelector(`[data-flag="${popup_flag}"]`);
    popup.style.display = 'block';
    popup.offsetHeight; // Force reflow to ensure transitions are applied
    popup.style.opacity = '1';
    let children = popup.children;
    for (let i = 0; i < children.length; i++) {
        children[i].classList.add('_show');
    }
    document.body.classList.add('_noScrollPopup');
}


function close_popup(ths = null) {
    // console.log(ths.closest('.popupBlock'))
    // console.log(document.querySelector('.popupBlock'))
    let popup = ths ? ths.closest('.popupBlock') : document.querySelector('.popupBlock');
    let children = popup.children;
    for (let i = 0; i < children.length; i++) {
        children[i].classList.remove('_show');
    }
    popup.style.opacity = '0';
    setTimeout(function() {
        popup.style.display = 'none';
        document.body.classList.remove('_noScrollPopup');
    }, 200);
}



// При клике открываем попап
document.querySelectorAll('._openPopup').forEach(function(element) {
    element.addEventListener('click', function () {
        var popup_number = this.getAttribute('data-popup');
        show_popup(popup_number);
    });
});

document.addEventListener('click', function(event) {
    let target = event.target;
    if (target.classList.contains('popupBlock')) {
        close_popup(target);
    } else if (target.classList.contains('popupItem')) {
        event.stopPropagation();
    }
});



