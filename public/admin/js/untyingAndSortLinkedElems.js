    
// добавляем (удаляем) в (из) input name=delete_elems айдишники элементов 
// короче говоря отвязываем элементы от категории  
function untyingLinkedElems(){

    const elemsList = document.querySelectorAll('.untieElement_js')
    const input_delete_elems = document.querySelector('[name="delete_elems"]')
    let arrIds = []

    elemsList.forEach(element => {
        element.addEventListener('click', function(event){
            event.preventDefault()
            
            const ths = this

            const elem_id = ths.dataset.id
            const parent = ths.closest('.catElemsList__li')

            const elemSort = document.querySelector('.elemsSort_js[data-id="'+elem_id+'"]')

            // если нажали отвязать фильм
            if(!ths.classList.contains('_remove')){
                ths.classList.add('_remove')
                parent.classList.add('_change')
                arrIds.push(elem_id)

                // если есть сортировка
                if(elemSort){
                    elemSort.classList.add('_disable')
                    elemSort.dataset.val = elemSort.value
                    elemSort.value = ''
                    // имитируем событие change
                    elemSort.dispatchEvent(new Event('change'))
                }
            }else{
                ths.classList.remove('_remove')
                parent.classList.remove('_change')
                arrIds = arrIds.filter(item => item !== elem_id);

                // если есть сортировка
                if(elemSort){
                    elemSort.classList.remove('_disable')
                    elemSort.value = elemSort.dataset.val
                    // имитируем событие change
                    elemSort.dispatchEvent(new Event('change'))
                }
            }

            let joinedString  = arrIds.join(', ')
            input_delete_elems.value = joinedString
            
        })
    });
}

// ================================================================================
// ================================================================================
// Сортировка привязанных элементов ===============================================
function sortLinkedElems(name_input){
    const inputsList = document.querySelectorAll('.elemsSort_js')
    if(inputsList.length > 0){
        let elemsObject = {}
        
        // создаем объект в котором будут храниться ключи они же id элементов и индекс сортировки и помещаем их в input
        inputsList.forEach((input) => {
            input.addEventListener('change', function(){
                changeInputsValue()
            })
        })

        // функция добавляем в объект ключ - значение (id - value) и записывает их в input[name=sort_films]
        function changeInputsValue(){
            inputsList.forEach(input => {
                elemsObject[input.dataset.id] = input.value
            })
            document.querySelector("input[name="+name_input+"]").value = JSON.stringify(elemsObject)
        }

        changeInputsValue()
    }

}
// ================================================================================
// ================================================================================
// ================================================================================
