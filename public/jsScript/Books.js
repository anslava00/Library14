function chooseAuthor (select, toSelect){
    let element = document.getElementById(select);
    let toElement = document.getElementById(toSelect);
    let option = document.createElement('option');
    option.text = element.options[element.selectedIndex].text;
    option.value = element.value;
    toElement.size = toElement.childElementCount + 1;
    element.size = element.childElementCount - 1;
    toElement.appendChild(option);
    element.remove(element.selectedIndex);
}

function createArray(){
    let parent = document.getElementById("form");
    let toElement = document.getElementById("authorChoose");
    let input = document.createElement("input");
    input.type = 'text';
    input.name = "countAuthor";
    input.hidden = true;
    input.value = toElement.childElementCount;
    parent.appendChild(input);
    for (let i = 0; i < toElement.childElementCount; i++){
        let input = document.createElement("input");
        toElement.selectedIndex = i;
        input.type = 'text';
        input.hidden = true;
        input.name = "author"+i;
        input.value = toElement.value;
        parent.appendChild(input);
    }
}

function onFileSelected(event) {
    let selectedFile = event.target.files[0];
    let reader = new FileReader();

    let imgtag = document.getElementById("myimage");
    imgtag.title = selectedFile.name;

    reader.onload = function(event) {
        imgtag.src = event.target.result;
    };

    reader.readAsDataURL(selectedFile);
}