// добавление и удаление изображения в разделе создание поста
let input = null 
let uploadImg = null
let input_delete_img = null 

function deleteEditImg(param = null){
    input = document.getElementById('add_img')

    if(input){
        input_delete_img = document.querySelector('[name="delete_img"]')

        input.addEventListener('change', function(){
            if (input.files && input.files[0]) {
                let reader = new FileReader()
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    if(param){
                        input_delete_img.value = param 
                    }
                    const elemImg = `<div class="uploadImg _mt20">
                            <img src="${e.target.result}"/>
                            <span class="closeIcon"><svg><use xlink:href="#close"/></svg></span>
                        </div>`
                    uploadImg = document.querySelector('.uploadImg')
                    console.log(uploadImg)

                    if(uploadImg){
                        uploadImg.remove()
                    }
                    input.parentNode.insertAdjacentHTML('beforebegin', elemImg);

                    // удаляем добавленный файл при клике на closeIcon
                    const closeUploadImg = document.querySelector('.uploadImg .closeIcon')
                    closeUploadImg.addEventListener('click', function(){
                        deleteInputImg(param)
                    })
                }
                
            }
        })
    }
}

function deleteInputImg(param = null){
    console.log(param)
    input.value = ''
    if(param){
        input_delete_img.value = '1';//param
    }
    document.querySelector('.uploadImg').remove()
}