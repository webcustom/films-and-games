// собираем id всех checked элементов в массив и выводим значения этого массива в input
// массовое выделение и удаление элементов в списке
function checkedListElems(){
    const input = document.getElementById('deleteElemsInput_js')
    const elems = document.querySelectorAll('.itemCheckbox_js input')
    const arrChecked = []
    const buttonDel = document.querySelector('.deleteCheckedButton_js')

    elems.forEach(element => {
        element.addEventListener('change', function(){
            const elemId = +this.name
            if (this.checked) {
                arrChecked.push(elemId)
            }else{
                let index = arrChecked.indexOf(elemId)
                // если элемент найден в массиве, удаляем
                if (index !== -1) {
                    arrChecked.splice(index, 1);
                }else{
                    console.error('Элемент с таким id не найден в массиве')
                }
            }
            buttonHide()
        
            // записываем значения из массива в input
            let values = arrChecked.join(',')
            input.value = values
        })
    });

    function buttonHide(){
        if(arrChecked.length === 0){
            // console.log(arrChecked)
            buttonDel.classList.add('_disable')
        }else{
            buttonDel.classList.remove('_disable')
        }
    }
    buttonHide()
}