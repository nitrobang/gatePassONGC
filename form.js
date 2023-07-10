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
  cell1.innerHTML = "<input type='hidden' name='serial_number[]'>" + rowCount + ". </input>";
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

$(document).ready(function() {
  $('input[name="return"]').change(function() {
      if ($(this).val() === '1') {
          $('#returnDateForm').show();
      } else {
          $('#returnDateForm').hide();
      }
  });
});
function printFunction() {
    var printWindow = window.open('', '', 'height=500, width=500');
    
    // Retrieve the CSS stylesheets
    var stylesheets = document.getElementsByTagName('link');
    for (var i = 0; i < stylesheets.length; i++) {
      var stylesheet = stylesheets[i];
      if (stylesheet.rel.toLowerCase() == 'stylesheet') {
        // Create a new link element in the print window and set its attributes
        var printStylesheet = document.createElement('link');
        printStylesheet.setAttribute('rel', 'stylesheet');
        printStylesheet.setAttribute('type', 'text/css');
        printStylesheet.setAttribute('href', stylesheet.href);
        // Append the link element to the head of the print window
        printWindow.document.head.appendChild(printStylesheet);
      }
    }
    
    // Get the content to be printed
    var divContents = document.getElementById('print').innerHTML;
    
    // Write the content to the print window and print it
    printWindow.document.write('<html><head><title>Print</title></head><body>' + divContents + '</body></html>');
    printWindow.document.close();
    printWindow.print();
  }
  
function findso(value) {
  // Create an AJAX object
  var xhttp = new XMLHttpRequest();

  // Define the callback function
  xhttp.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
          // Parse the response as JSON
          var response = JSON.parse(this.responseText);

          // Get the autocomplete list
          var autocompleteList = document.querySelector('.autocomplete-list1');
          var sugges = document.querySelector('.result1');
          // Clear the previous suggestions
          autocompleteList.innerHTML = '';

          if (value === '') {
              // If the input field is empty, hide the autocomplete list and change the sentence
              autocompleteList.style.display = 'none';
              sugges.innerHTML = "Signatory Officer:";
              disableSubmitButton();
          } else {
              // Display the matching empname, designation, and cpfno as clickable suggestions
              response.forEach(function (user) {
                  var empname = user.empname;
                  var designation = user.designation;
                  var cpfno = user.cpfno;
                  if (document.querySelector('.sugges input[name="signatory"]').value == cpfno) {
                      sugges.innerHTML = empname + ' - ' + designation;
                      autocompleteList.style.visibility = 'hidden';
                  } else {
                      sugges.innerHTML = "Signatory Officer:";
                      autocompleteList.style.visibility = 'visible';
                      disableSubmitButton();
                      var suggestion = document.createElement('li');
                      suggestion.textContent = empname + ' - ' + designation;
                      suggestion.addEventListener('click', function () {
                          // Fill the input field with the cpfno of the selected suggestion
                          document.querySelector('.sugges input[name="signatory"]').value = cpfno;
                          autocompleteList.innerHTML = '';
                          sugges.innerHTML = 'Signatory Officer: ' + empname + ' - ' + designation;
                          enableSubmitButton();
                      });

                      autocompleteList.appendChild(suggestion);
                  }

              });

              // Show the autocomplete list
              autocompleteList.style.display = 'block';
          }
      }
  };

  // Make a GET request to the PHP script
  xhttp.open('GET', 'checkdetail.php?cpfno=' + value + '&searchno=2', true);
  xhttp.send();
}

function findet(value) {
  // Create an AJAX object
  var xhttp = new XMLHttpRequest();

  // Define the callback function
  xhttp.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
          // Parse the response as JSON
          var response = JSON.parse(this.responseText);

          // Get the autocomplete list
          var autocompleteList = document.querySelector('.autocomplete-list');
          var sugges = document.querySelector('.result');
          // Clear the previous suggestions
          autocompleteList.innerHTML = '';

          if (value === '') {
              // If the input field is empty, hide the autocomplete list and change the sentence
              autocompleteList.style.display = 'none';
              sugges.innerHTML = "Forwarded to:";
              disableSubmitButton();
          } else {
              // Display the matching empname, designation, and cpfno as clickable suggestions
              response.forEach(function (user) {
                  var empname = user.empname;
                  var designation = user.designation;
                  var cpfno = user.cpfno;
                  if (document.querySelector('.sugg input[name="fors"]').value == cpfno) {
                      sugges.innerHTML = 'Forwarding to: ' + empname + ' - ' + designation;
                      autocompleteList.style.visibility = 'hidden';
                  } else {
                      sugges.innerHTML = "Forwarded to:";
                      autocompleteList.style.visibility = 'visible';
                      disableSubmitButton();
                      var suggestion = document.createElement('li');
                      suggestion.textContent = 'Forward to: ' + empname + ' - ' + designation;
                      suggestion.addEventListener('click', function () {
                          // Fill the input field with the cpfno of the selected suggestion
                          document.querySelector('.sugg input[name="fors"]').value = cpfno;
                          autocompleteList.innerHTML = '';
                          sugges.innerHTML = 'Forwarding to: ' + empname + ' - ' + designation;
                          enableSubmitButton();
                      });

                      autocompleteList.appendChild(suggestion);
                  }

              });

              // Show the autocomplete list
              autocompleteList.style.display = 'block';
          }
      }
  };

  // Make a GET request to the PHP script
  xhttp.open('GET', 'checkdetail.php?cpfno=' + value + '&searchno=1', true);
  xhttp.send();
}

function enableSubmitButton() {
  var submitButton = document.getElementById('submitButton');
  submitButton.disabled = false;
}

function disableSubmitButton() {
  var submitButton = document.getElementById('submitButton');
  submitButton.disabled = true;
}

function showOtherOption(selectElement) {
  var otherOptionContainer = document.getElementById('otherOptionContainer');
  var otherOptionInput = otherOptionContainer.querySelector('input[name="otherOption"]');
  if (selectElement.value === 'other') {
      otherOptionContainer.style.display = 'block';
      otherOptionInput.setAttribute('required', 'required');
  } else {
      otherOptionContainer.style.display = 'none';
      otherOptionInput.removeAttribute('required');
  }
}
