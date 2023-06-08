document.getElementById("addRowBtn").addEventListener("click", function() {
        var table = document.getElementById("myTable");
        var row = table.insertRow();
        var cells = [];

        for (var i = 0; i < 5; i++) {
            cells[i] = row.insertCell(i);
            cells[i].innerHTML = `<input type="text" name="srno[]">`;
        }

        var deleteButtonCell = row.insertCell(5);
        deleteButtonCell.innerHTML = `<button class="removeRowBtn" type="button">Remove</button>`;
});

// Event delegation to handle remove button clicks
document.addEventListener("click", function(event) {
        if (event.target.classList.contains("removeRowBtn")) {
            var row = event.target.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }
});
