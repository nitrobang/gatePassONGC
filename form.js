// Get the elements needed
var addRowButton = document.getElementById("addrow");
var readForm = document.getElementById("readform");
function addrow() {
    // Clone the form row
    var clonedRow = readForm.firstElementChild.cloneNode(true);

    // Clear the input values in the cloned row
    var inputFields = clonedRow.getElementsByTagName('input');
    for (var i = 0; i < inputFields.length; i++) {
        inputFields[i].value = '';
    }

    // Append the cloned row to the table body
    readForm.appendChild(clonedRow);
}
// Add click event listener to the add row button
if(addRowButton){
    console.log('addrow click event');

}
else{
    console.log('addrow click event not implemented');
}

