function addRow() {
    var table = document.getElementById("dynamic-table");
    var rowCount = table.rows.length;

    var row = table.insertRow(-1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);
    var cell6 = row.insertCell(5);
    cell1.innerHTML = "<input type='hidden' name='serial_number[]'>"+rowCount+". </input>";
    cell2.innerHTML = "<input type='text' name='description[]'>";
    cell3.innerHTML = "<input type='text' name='num[]'>";
    cell4.innerHTML = "<input type='text' name='dispatchnotes[]'>";
    cell5.innerHTML = "<input type='text' name='remarks[]'>";
    if (table.rows.length > 2) {
        cell6.innerHTML = "<button type='button' onclick='removeRow(this)'>Remove Row</button>";
    }
}

function removeRow(button) {
    var row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
}