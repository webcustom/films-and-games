<div class="popupBlock" data-flag="confirmDelete">
    <div class="popupConfirm popupItem">
        <p class="popupConfirm__title" id="titleText_js"></p>
        <p id="titleElemDelete_js"></p>
        <div class="popupConfirm__buttons">
            <button class="button_1" onclick="close_popup(this)">Отмена</button>
            <button type="submit" class="button_1" id="buttonDelete_js" onclick="confirmDelete('formDelete')">Удалить</button>
        </div>
    </div>
</div>


<form id="formDelete" method="POST" action="{{ $route }}" style="opacity: 0; visibility: hidden">
    @method('DELETE')
    @csrf
    <input type="text" name="field_delete_id" id="delete_id_js" readonly>

    {{-- @if(isset($title_1))
        <input type="text" name="title_1" value="{{ $title_1 }}">
        <script>
            const title_1 = document.querySelector('input[name="title_1"]').value
        </script>
    @endif

   
    @if(isset($title_2))
        <input type="text" name="title_2" value="{{ $title_2 }}">
        <script>
            const title_2 = document.querySelector('input[name="title_2"]').value
        </script>
    @endif --}}
        

</form>


<script>

    // const title_1 = document.querySelector('input[name="title_1"]').value
    // const title_2 = document.querySelector('input[name="title_2"]').value

    // console.log(title_1)
    // добавляем в поле delete_id_js id удаляемого элемента и title в текст попапа
    const delButton = document.querySelectorAll('.deleteElem_js')
    delButton.forEach(element => {
        element.addEventListener('click', function(){
            document.getElementById('buttonDelete_js').style.display = 'block'

            let elemId = this.dataset.id
            let elemTitle = this.dataset.title
            let errorText = this.dataset.error
            console.log(errorText)
            document.getElementById('delete_id_js').value = elemId

            if(errorText && errorText !== ''){
                document.getElementById('buttonDelete_js').style.display = 'none'
                document.getElementById('titleText_js').innerHTML = errorText
            }else if(elemTitle){
                document.getElementById('titleText_js').innerHTML = elemTitle
            }
            // if(elemTitle){
            //     document.getElementById('titleText_js').innerHTML = elemTitle
            // }
            // document.getElementById('titleElemDelete_js').innerText = elemTitle
        })
    });


    // удаление через чекбоксы
    // берем значения из input на странице и помещаем их в input нашей псевдоформы
    const deleteCheckedButton = document.querySelector('.deleteCheckedButton_js')
    const deleteElemsInput = document.getElementById('deleteElemsInput_js')
    if(deleteCheckedButton){
        // console.log(deleteElemsInput)
        deleteCheckedButton.addEventListener('click', function(){
            const deleteIds = deleteElemsInput.value
            document.getElementById('delete_id_js').value = deleteIds
            console.log(this.dataset.title)
            document.getElementById('titleText_js').innerHTML = this.dataset.title
        })
    }


    // функция отправляющая форму с id удаляемых файлов
    function confirmDelete(id) {
        document.getElementById(id).submit();
    }


</script>