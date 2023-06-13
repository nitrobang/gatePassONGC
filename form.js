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
function findet(value) {
    // Create an AJAX object
    var xhttp = new XMLHttpRequest();
  
    // Define the callback function
    xhttp.onreadystatechange = function() {
      if (this.readyState === 4 && this.status === 200) {
        // Parse the response as JSON
        var response = JSON.parse(this.responseText);
  
        // Get the autocomplete list
        var autocompleteList = document.querySelector('.autocomplete-list');
  
        // Clear the previous suggestions
        autocompleteList.innerHTML = '';
  
        if (value === '') {
          // If the input field is empty, hide the autocomplete list
          autocompleteList.style.display = 'none';
        } else {
          // Display the matching username, email, and cpfno as clickable suggestions
          response.forEach(function(user) {
            var username = user.username;
            var email = user.email;
            var cpfno = user.cpfno;
  
            var suggestion = document.createElement('li');
            suggestion.textContent = 'Forwarded to: ' + username + ' - ' + email;
            suggestion.addEventListener('click', function() {
              // Fill the input field with the cpfno of the selected suggestion
              document.querySelector('input[name="fors"]').value = cpfno;
            });
  
            autocompleteList.appendChild(suggestion);
          });
  
          // Show the autocomplete list
          autocompleteList.style.display = 'block';
        }
      }
    };
  
    // Make a GET request to the PHP script
    xhttp.open('GET', 'checkdetail.php?cpfno=' + value, true);
    xhttp.send();
}
  
  