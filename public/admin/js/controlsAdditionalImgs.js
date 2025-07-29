// скрипты для управления дополниьельными изображениями

function addImgsCreateInput(){
    const button = document.getElementById('additionalImg_js')
    button.addEventListener('click', function(){
        const imgAddElem = document.createElement('div')
        imgAddElem.classList.add('addImg')
        imgAddElem.innerHTML = `<div class="addImg__content">
                    <input class="additionalImgInput_js" type="file" name="additional_imgs[]" id="id-${Date.now()}" onchange="showThumbnail(this)">
                    <input type="text" class="input_1" placeholder="Описание к изображению" name="additional_imgs_text[]" value="">
                    <div class="addImg__bottom">
                        <label class="button_1" for="id-${Date.now()}">Выбрать файл</label>
                        <input type="text" class="input_1" placeholder="Сорт-ка" name="additional_imgs_sort[]" value="">
                    </div>
                </div>
                <span class="closeIcon _create" onclick="deleteAditionalImg(this)"><svg><use xlink:href="#close"/></svg></span>
                `
        // console.log(this.parentNode.querySelector('.additionImgsList'))

        this.parentNode.querySelector('.additionImgsList').appendChild(imgAddElem)//.insertBefore(imgAddElem, this)
        
        // this.parentNode.querySelector('.additionImgsList').insertAdjacentHTML('beforeappendChildend', imgAddElem)//.insertBefore(imgAddElem, this)
    })
}


function showThumbnail(ths){

    if (ths.files && ths.files[0]) {
        let reader = new FileReader()
        reader.readAsDataURL(ths.files[0])
        reader.onload = function (e) {
            const elemName = `<p>${ths.files[0].name}</p>`
            
            const elemImg = `<div class="addImg__imgWrap">
                    <div class="addImg__img">
                        <img src="${e.target.result}"/>
                        ${elemName}
                    </div>
                </div>`

            // const container = ths.parentNode.parentNode;
            // console.log(ths.parentNode.parentNode.querySelector('.addImg__imgWrap'))
            if(ths.parentNode.parentNode.querySelector('.addImg__imgWrap')){
                ths.parentNode.parentNode.querySelector('.addImg__imgWrap').remove()
            }
            ths.parentNode.parentNode.insertAdjacentHTML('afterbegin', elemImg)

            // ths.parentNode.insertAdjacentHTML('afterbegin', elemName)
            ths.parentNode.parentNode.classList.add('_uploaded')
        }
    }
}


let arrIds = []
function deleteAditionalImg(ths){

    if(ths.classList.contains('_create')){
        ths.parentNode.remove()
    }else{
        input_delete_imgs = document.querySelector('[name="delete_additional_img"]')
        const dataIndex = ths.dataset.index
        
        if(!ths.parentNode.classList.contains('_remove')){
            ths.parentNode.classList.add('_remove')
            arrIds.push(dataIndex)
        }else{
            ths.parentNode.classList.remove('_remove')
            arrIds = arrIds.filter(item => item !== dataIndex)
        }

        let joinedString  = arrIds.join(', ')
        input_delete_imgs.value = joinedString
    }
}


